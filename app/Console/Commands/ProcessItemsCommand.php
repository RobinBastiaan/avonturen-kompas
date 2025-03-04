<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\Comment;
use App\Models\ExtractedItem;
use App\Models\Hits;
use App\Models\Item;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use DaveChild\TextStatistics\TextStatistics;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ProcessItemsCommand extends Command
{
    protected $signature = 'app:process:items';
    protected $description = 'Process not yet applied items, and create related entities if not yet present.';

    protected TextStatistics $textStatistics;
    protected array $existingItemIds;
    protected array $existingCategoryIds;
    protected array $existingTagIds;
    protected int $createdCount = 0;
    protected int $updatedCount = 0;
    protected int $notChangedCount = 0;

    public function __construct()
    {
        $this->textStatistics = new TextStatistics();

        parent::__construct();
    }

    public function handle(): int
    {
        $startTime = now();

        $unprocessedItemDate = $this->getUnprocessedItemDate();
        while ($unprocessedItemDate) {
            $this->processItemsForDate($unprocessedItemDate);
            $unprocessedItemDate = $this->getUnprocessedItemDate();
        }

        $totalTime = ceil(now()->diffInSeconds($startTime, true));
        $this->newLine();
        $this->info("Total item processing time: {$totalTime} seconds");
        $this->info("Items created: {$this->createdCount}");
        $this->info("Items updated: {$this->updatedCount}");
        $this->info("Items unchanged: {$this->notChangedCount}");
        $this->info("Total items processed: " . ($this->createdCount + $this->updatedCount + $this->notChangedCount));

        return CommandAlias::SUCCESS;
    }

    protected function getUnprocessedItemDate(): ?Carbon
    {
        return ExtractedItem::query()
            ->whereNull('applied_to')
            ->first()
            ?->extracted_at;
    }

    protected function processItemsForDate(Carbon $firstItemDate): int
    {
        $skippedExtractedItems = collect();

        $this->existingItemIds = [];
        $this->existingCategoryIds = [];
        $this->existingTagIds = [];

        // Retrieve all existing model ID's to know what models are no longer in the extracted items and thus ready to be removed.
        // Only if there are no items already processed for this date.
        $unprocessedItemDate = $this->getUnprocessedItemDate();
        $processedItemDate = ExtractedItem::query()
            ->whereNotNull('applied_to')
            ->orderByDesc('id')
            ->first()
            ?->extracted_at;

        if ($unprocessedItemDate && $processedItemDate && !$unprocessedItemDate->isSameDay($processedItemDate)) {
            $this->existingItemIds = Item::pluck('id')->toArray();
            $this->existingCategoryIds = Category::pluck('id')->toArray();
            $this->existingTagIds = Tag::pluck('id')->toArray();
        }

        /** @var Collection|ExtractedItem[] $extractedItems */
        $extractedItems = ExtractedItem::query()
            ->whereNull('applied_to')
            ->whereDate('extracted_at', $firstItemDate->format('Y-m-d'))
            ->get();

        $extractedItemsCount = $extractedItems->count();
        if ($extractedItemsCount === 0) {
            $this->error('No items left to be processed.');
            return CommandAlias::FAILURE;
        }

        $progressBar = $this->output->createProgressBar($extractedItemsCount);
        $progressBar->start();

        foreach ($extractedItems as $extractedItem) {
            $skippedExtractedItem = $this->processItem($extractedItem, true);

            if ($skippedExtractedItem) {
                $skippedExtractedItems->push($skippedExtractedItem);
            } else {
                $progressBar->advance();
            }
        }
        foreach ($skippedExtractedItems as $skippedExtractedItem) {
            $this->processItem($skippedExtractedItem, false);

            $progressBar->advance();
        }

        $progressBar->finish();

        // Recalculate counts.
        $this->recalculateUseCounts();

        // Remove models no longer present in the extracted items, as apparently they have been deleted in mean time.
        Item::whereIn('id', $this->existingItemIds)->get()->each(function (Item $item) {
            $this->warn("Deleting item {$item->id}: {$item->title} because it is no longer present in the extracted data.");
            $item->delete();
        });
        Category::whereIn('id', $this->existingCategoryIds)->get()->each(function (Category $category) {
            $this->warn("Deleting category {$category->id}: {$category->name} because it is no longer present in the extracted data.");
            $category->delete();
        });
        Tag::whereIn('id', $this->existingTagIds)->get()->each(function (Tag $tag) {
            $this->warn("Deleting tag {$tag->id}: {$tag->name} because it is no longer present in the extracted data.");
            $tag->delete();
        });

        return CommandAlias::SUCCESS;
    }

    /**
     * Process an item and create related entities if they do not exist yet.
     */
    protected function processItem(ExtractedItem $extractedItem, $canSkip): ?ExtractedItem
    {
        $item = $this->findOrCreateItem($extractedItem);
        // Remove all found items from the existing item array.
        $this->existingItemIds = array_diff($this->existingItemIds, [$item->id]);
        $isCamp = $this->isCamp($extractedItem->raw_content);

        // Items that are an activity of a camp, do not know what camp they are part of. And since it is impossible to relate a camp to an
        // activity that does not yet exist, we need to check if a camp contains not yet created activities and skip that camp for now.
        if ($canSkip && $isCamp && $this->hasNoneExistingActivities($extractedItem->raw_content)) {
            $this->warn("Skipped ExtractedItem {$extractedItem->id} because it is a camp with activities that do not exist yet.");

            return $extractedItem;
        }

        // Create new attributes array to compare with existing.
        $title = $this->extractTitle($extractedItem->raw_content);
        $newAttributes = [
            'original_id'  => $extractedItem->original_id,
            'is_published' => true,
            'title'        => $title,
            'slug'         => $extractedItem->original_slug,
            'is_camp'      => $isCamp,
            'camp_length'  => $this->extractCampLength($extractedItem->raw_content),
            'summary'      => $this->extractSummary($extractedItem->raw_content),
            'description'  => $this->extractDescription($extractedItem->raw_content, $title),
            'requirements' => $this->extractRequirements($extractedItem->raw_content),
            'tips'         => $this->extractTips($extractedItem->raw_content),
            'safety'       => $this->extractSafety($extractedItem->raw_content),
            'hits'         => $extractedItem->hits,
            'created_by'   => $extractedItem->author_name ? $this->extractCreatedBy($extractedItem->author_name)->id : null,
            // Note: updated_by is not given in the extracted item, so we leave it null.
            'created_at'   => $extractedItem->published_at,
            'updated_at'   => $extractedItem->modified_at,
        ];

        // Check if any attributes are different.
        $hasChanges = false;
        foreach ($newAttributes as $key => $value) {
            if ($item->$key != $value) {
                $hasChanges = true;
                break;
            }
        }

        if ($hasChanges || $item->wasRecentlyCreated) {
            // Apply new attributes and save.
            foreach ($newAttributes as $key => $value) {
                $item->$key = $value;
            }
            $item->word_count = $this->calculateWordCounts($item);
            $item->flesch_reading_ease = $this->calculateFleschReadingEase($item);
            $item->save();

            // Find or create related entities.
            $this->newLine();
            $item->createdBy()->associate($extractedItem->author_name ? $this->extractCreatedBy($extractedItem->author_name) : null);
            $item->categories()->sync($this->extractCategories($extractedItem->raw_content));
            $item->tags()->sync($this->extractTags($extractedItem->raw_content));
            $this->extractComments($item->id, $extractedItem->raw_content);
            $this->extractHits($item->id, $extractedItem->extracted_at, $extractedItem->hits);
            $item->save();

            if ($isCamp) {
                $linkedActivities = $this->extractActivities($extractedItem->raw_content);
                $syncData = $linkedActivities->mapWithKeys(function (Item $activity, $index) use ($item) {
                    return [$activity->id => [
                        'camp_id'    => $item->id,
                        'day_number' => 1, // The original linked activities do not have a day number, so set it arbitrarily.
                        'sort_order' => $index + 1,
                    ]];
                });

                $item->activities()->sync($syncData);
            }
        }

        if (!$hasChanges) {
            $this->notChangedCount++;
        } elseif ($item->wasRecentlyCreated) {
            $this->createdCount++;
            $this->info("Created Item {$item->id} with data from ExtractedItem {$extractedItem->id} (original id {$extractedItem->original_id}).");
        } else {
            $this->updatedCount++;
            $this->info("Updated Item {$item->id} with data from ExtractedItem {$extractedItem->id} (original id {$extractedItem->original_id}).");
        }

        // Mark the extraction as applied regardless of whether changes were made.
        $extractedItem->applied_to = $item->id;
        $extractedItem->save();

        return null;
    }

    /**
     * Count the words in all content columns to represent the amount of words displayed for the record.
     */
    protected function calculateWordCounts(Item $item): int
    {
        $content = implode(' ', array_filter([
            $item->description,
            $item->requirements,
            $item->tips,
            $item->safety,
        ]));

        return str_word_count(strip_tags($content));
    }

    protected function calculateFleschReadingEase(Item $item): int
    {
        // Only fields that contain sentences are considered, so for example the requirements are omitted.
        $content = implode(' ', array_filter([
            $item->description,
            $item->tips,
            $item->safety,
        ]));

        // Add dots after whitelisted HTML elements, so their content is counted as separate sentences.
        $content = preg_replace('/\s*<\/(?:p|div|h[1-6]|li|br)>\s*/i', '. ', $content);
        $content = strip_tags($content);

        return $this->textStatistics->fleschKincaidReadingEase($content);
    }

    /**
     * To prevent Items from being added twice, check if it was processed before to update that Item instead.
     */
    protected function findOrCreateItem(ExtractedItem $extractedItem): Item
    {
        // Check if this item was not processed before.
        $existingItem = Item::query()
            ->where('original_id', $extractedItem->original_id)
            ->first();

        // And if so, overwrite this existing Item instead of creating a new one.
        return $existingItem ?: new Item();
    }

    protected function hasNoneExistingActivities(string $content): bool
    {
        $ids = $this->extractOriginalActivityIds($content);

        return Item::query()->whereIn('original_id', $ids)->count() !== count($ids);
    }

    protected function extractOriginalActivityIds(string $content): array
    {
        if (!preg_match('/<h2>Gekoppelde activiteiten<\/h2>\s*<ul>(.*?)<\/ul>/s', $content, $section)) {
            return [];
        }

        preg_match_all('/\/item\/(\d+)/', $section[1], $matches);

        return array_map('intval', $matches[1]);
    }

    protected function extractActivities(string $content): Collection
    {
        $slugs = $this->extractActivitySlugs($content);

        return Item::query()->whereIn('slug', $slugs)->get();
    }

    protected function extractActivitySlugs(string $content): array
    {
        if (!preg_match('/<h2>Gekoppelde activiteiten<\/h2>\s*<ul>(.*?)<\/ul>/s', $content, $section)) {
            return [];
        }

        preg_match_all('/\/item\/(\d+)-[^"]+/', $section[1], $matches);

        // Items do not know their original id, so we need to try to find them by slug.
        return array_map(function ($url) {
            return Str::slug(substr(strstr(substr(strrchr($url, '/'), 1), '-'), 1));
        }, $matches[0]);
    }

    protected function extractTitle(string $content): string
    {
        if (preg_match('/<h2 class="itemTitle">\s*(.*?)\s*<\/h2>/s', $content, $matches)) {
            // First strip HTML tags, then decode entities to prevent HTML injection.
            // This allows "&amp;" to be converted back to "&" for example.
            return trim(htmlspecialchars_decode(strip_tags($matches[1]), ENT_QUOTES));
        }
        return '';
    }

    protected function extractSummary(string $content): string
    {
        if (preg_match('/<h2>(?:Waarom \/ doel van de activiteit|Korte omschrijving)<\/h2>\s*(.*?)(?=<h2>|<\/div>)/s', $content, $matches)) {
            return trim(strip_tags($matches[1]));
        }

        return '';
    }

    protected function extractDescription(string $content, string $title): string
    {
        if (preg_match('/<h2>(?:Beschrijving van de activiteit|Themaverhaal)<\/h2>(.*?)(?=<h2>|<\/div>)/s', $content, $matches)) {
            $sanitized = $this->sanitize($matches[1]);

            if ($sanitized === null) {
                $this->warn("No description found for Item '{$title}'.");
                return '';
            }

            return $sanitized;
        }

        return '';
    }

    protected function extractRequirements(string $content): ?string
    {
        if (preg_match('/<h2>(?:Benodigd materiaal|Globale planning)<\/h2>(.*?)(?=<h2>|<\/div>)/s', $content, $matches)) {
            return $this->sanitize($matches[1]);
        }

        return null;
    }

    protected function extractTips(string $content): ?string
    {
        if (preg_match('/<h2>Tips<\/h2>(.*?)(?=<h2>|<\/div>)/s', $content, $matches)) {
            return $this->sanitize($matches[1]);
        }

        return null;
    }

    protected function extractSafety(string $content): ?string
    {
        if (preg_match('/<h2>Veiligheid<\/h2>(.*?)(?=<h2>|<\/div>)/s', $content, $matches)) {
            return $this->sanitize($matches[1]);
        }

        return null;
    }

    protected function isCamp(string $content): bool
    {
        return str_contains($content, 'Kampthema\'s');
    }

    protected function extractCampLength(string $content): ?int
    {
        if (!$this->isCamp($content)) {
            return null;
        }

        if (!preg_match('/<div class="fullk2categories">(.*?)<\/div>/s', $content, $match)) {
            return null;
        }

        if (str_contains($match[1], 'weekend')) {
            return 3;
        }

        if (str_contains($match[1], '4 dagen')) {
            return 4;
        }

        if (str_contains($match[1], 'week')) {
            return 7;
        }

        return null;
    }

    protected function extractCreatedBy(string $authorName): User
    {
        return cache()->remember("user.{$authorName}", 3600, function () use ($authorName) {
            $user = User::firstOrCreate(
                ['name' => $authorName],
                ['email' => Str::slug($authorName) . '@un.known', 'password' => '']
            );

            if ($user->wasRecentlyCreated) {
                $this->info("Created new user: {$user->name}");
            }

            return $user;
        });
    }

    protected function extractCategories(string $content): ?Collection
    {
        if (!preg_match('/<div class="fullk2categories">(.*?)<\/div>/s', $content, $sidebarMatch)) {
            return null;
        }

        preg_match_all('/<h2>([^<]*)<\/h2>|<img[^>]*title="([^"]*)"[^>]*>/s', $sidebarMatch[1], $matches);

        $categories = collect();
        foreach ($matches[0] as $index => $fullMatch) {
            if ($matches[1][$index]) { // This is an H2 header, and thus a category group.
                $categoryGroup = cache()->remember("category_group.{$matches[1][$index]}", 3600, function () use ($matches, $index) {
                    /** @var CategoryGroup $categoryGroup */
                    $categoryGroup = CategoryGroup::firstOrNew(['name' => trim(html_entity_decode($matches[1][$index]))]);

                    if ($categoryGroup->wasRecentlyCreated) {
                        $categoryGroup->is_published = true;
                        $categoryGroup->is_available_for_activities = true;
                        $categoryGroup->is_available_for_camps = true;
                        $categoryGroup->save();

                        $this->info("Created new category group: {$categoryGroup->name}");
                    }

                    return $categoryGroup;
                });

                // Note: do not push a category group to be related to the Item, because it is implied from category relationship.
                $parentCategoryGroup = $categoryGroup;
            } elseif ($matches[2][$index]) { // This is an img title, and thus a category.
                if (!isset($parentCategoryGroup)) {
                    throw new \Exception("Parent category for {$matches[2][$index]} is null.");
                }

                $category = cache()->remember("category.{$matches[2][$index]}.group.{$parentCategoryGroup->id}", 3600, function () use ($matches, $index, $parentCategoryGroup) {
                    /** @var Category $category */
                    $category = Category::firstOrNew([
                        'name'              => trim(html_entity_decode($matches[2][$index])),
                        'category_group_id' => $parentCategoryGroup->id,
                    ]);
                    $category->is_published = true;
                    $category->category_group_id = $parentCategoryGroup->id;
                    $category->save();

                    if ($category->wasRecentlyCreated) {
                        $this->info("Created new category for category group {$parentCategoryGroup->name}: {$category->name}");
                    }

                    return $category;
                });

                $categories->push($category);
            }
        }

        // Remove all found categories from the existing category array.
        $this->existingCategoryIds = array_diff($this->existingCategoryIds, $categories->pluck('id')->all());

        return $categories;
    }

    protected function extractTags(string $content): ?Collection
    {
        if (!preg_match_all('/<a href="\/component\/k2\/itemlist\/tag\/[^"]*">([^<]+)<\/a>/', $content, $matches)) {
            return null;
        }

        $tags = collect($matches[1])->map(function ($name) {
            // Fix known misspellings of tags because the original data doesn't support hyphens.
            $name = $name === 'JOTA JOTI' ? 'JOTA-JOTI' : $name;
            $name = $name === 'Holi Phagwa' ? 'Holi-Phagwa' : $name;

            return cache()->remember("tag.{$name}", 3600, function () use ($name) {
                /** @var Tag $tag */
                $tag = Tag::firstOrNew(['name' => trim(html_entity_decode($name))]);
                $tag->is_published = true;
                $tag->save();

                if ($tag->wasRecentlyCreated) {
                    $this->info("Created new tag: {$tag->name}");
                }

                return $tag;
            });
        });

        // Remove all found tags from the existing tag array.
        $this->existingTagIds = array_diff($this->existingTagIds, $tags->pluck('id')->all());

        return $tags;
    }

    protected function extractComments(int $itemId, string $content): void
    {
        if (!preg_match_all('/<li class="(?:even|odd)(?:\s+authorResponse)?">[^<]*'
            . '<span class="commentLink">.*?<\/span>[^<]*'
            . '<span class="commentDate">([^<]+)<\/span>[^<]*'
            . '<span class="commentAuthorName">[^<]*<a[^>]*>([^<]+)<\/a>[^<]*<\/span>[^<]*'
            . '<p>(.*?)<\/p>/s',
            $content,
            $matches,
            PREG_SET_ORDER
        )) {
            return;
        }

        collect($matches)->map(function ($match) use ($itemId) {
            /** @var Comment $comment */
            $comment = Comment::firstOrNew([
                'item_id' => $itemId,
                'text'    => trim(strip_tags($match[3])),
            ]);
            $comment->is_published = true;
            $comment->created_by = $this->extractCreatedBy(trim($match[2]))->id;
            $comment->created_at = $this->formatDate($match[1]);
            $comment->save();

            if ($comment->wasRecentlyCreated) {
                $this->info("Created new comment for item: {$comment->item_id}");
            }

            return $comment;
        });
    }

    /**
     * Build an historical overview of hits from extracted items.
     */
    protected function extractHits(int $itemId, Carbon $date, int $hits): void
    {
        Hits::firstOrCreate(['item_id' => $itemId, 'extracted_at' => $date], ['hits' => $hits]);
    }

    protected function formatDate(string $match): Carbon
    {
        // Set array of Dutch month names to English to translate it.
        $monthsDutchToEnglish = [
            'januari'   => 'January',
            'februari'  => 'February',
            'maart'     => 'March',
            'april'     => 'April',
            'mei'       => 'May',
            'juni'      => 'June',
            'juli'      => 'July',
            'augustus'  => 'August',
            'september' => 'September',
            'oktober'   => 'October',
            'november'  => 'November',
            'december'  => 'December',
        ];

        // Remove day name and comma, then translate month.
        $date = preg_replace('/^[^,]+,\s*/', '', trim($match));
        foreach ($monthsDutchToEnglish as $dutch => $english) {
            $date = str_replace($dutch, $english, $date);
        }

        return Carbon::createFromFormat('d F Y H:i', $date);
    }

    /**
     * Recalculate the use count for all categories and tags.
     * This is done at the end of the processing of items to reduce the amount of queries on these records.
     */
    protected function recalculateUseCounts(): void
    {
        $this->newLine(2);
        $this->info('Recalculating use counts...');

        DB::statement('
            UPDATE categories c
            SET use_count = (
                SELECT COUNT(DISTINCT ci.item_id)
                FROM category_item ci
                WHERE ci.category_id = c.id
            )
        ');

        // Set the use_count of category groups to be the sum of their categories.
        DB::statement('
            UPDATE category_groups cg
            SET use_count = (
                SELECT COALESCE(SUM(c.use_count), 0)
                FROM categories c
                WHERE c.category_group_id = cg.id
            )
        ');

        DB::statement('
            UPDATE tags t
            SET use_count = (
                SELECT COUNT(DISTINCT it.item_id)
                FROM item_tag it
                WHERE it.tag_id = t.id
            )
        ');

        $this->info('Use counts updated successfully.');
    }

    protected function sanitize($matches): ?string
    {
        $cleaned = mb_convert_encoding($matches, 'UTF-8', 'UTF-8');

        // Replace all types of NBSPs with regular spaces.
        $cleaned = str_replace(['\xC2\xA0', '\xA0', '&nbsp;'], ' ', $cleaned);

        // Strip all HTML attributes except href on anchor tags and src/style on img tags.
        $cleaned = preg_replace_callback(
            '/<((?!(?:a|img)\b)[a-z][a-z0-9]*)[^>]*?(\/?)>|<(a|img)\b([^>]*?(href|src)="([^"]*)"[^>]*?(style="[^"]*")?|[^>]*?(style="[^"]*")[^>]*?(href|src)="([^"]*)")([^>]*?)>/i',
            static function ($matches) {
                if (!isset($matches[3])) {
                    return '<' . $matches[1] . $matches[2] . '>';
                }

                $tag = $matches[3];
                $attrs = [];

                // Check for href/src attribute
                if (isset($matches[5]) && isset($matches[6])) {
                    $attrName = $matches[5];
                    $attrValue = $matches[6];
                    $attrs[] = "{$attrName}=\"{$attrValue}\"";
                } elseif (isset($matches[9]) && isset($matches[10])) {
                    $attrName = $matches[9];
                    $attrValue = $matches[10];
                    $attrs[] = "{$attrName}=\"{$attrValue}\"";
                }

                // Check for style attribute
                if ($tag === 'img' && !empty($matches[7])) {
                    $attrs[] = $matches[7];
                } elseif ($tag === 'img' && !empty($matches[8])) {
                    $attrs[] = $matches[8];
                }

                return "<{$tag} " . implode(' ', $attrs) . ($tag === 'img' ? ' />' : '>');
            },
            $cleaned
        );

        // Remove empty HTML elements, including those with only whitespace.
        $cleaned = preg_replace('/<([a-z][a-z0-9]*)[^>]*>\s*<\/\1>/i', '', $cleaned);
        $cleaned = preg_replace('/<([a-z][a-z0-9]*)[^>]*>\s*(?:<\1[^>]*>\s*<\/\1>\s*)*<\/\1>/i', '', $cleaned);

        // Remove all div and span tags but keep their content.
        $cleaned = preg_replace('/<\/?(?:div|span)>/', '', $cleaned);

        // Replace horizontal whitespace (spaces, tabs) with a single space, but preserve line breaks.
        $cleaned = preg_replace('/[^\S\r\n]+/', ' ', $cleaned);

        // Replace multiple line breaks with a single one.
        $cleaned = preg_replace('/\R{2,}/', "\n", $cleaned);

        $cleaned = preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', $cleaned);

        $cleaned = trim(\Normalizer::normalize($cleaned, \Normalizer::FORM_C));

        return empty($cleaned) ? null : $cleaned;
    }
}
