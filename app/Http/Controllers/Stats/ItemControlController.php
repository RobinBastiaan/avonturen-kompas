<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemControlController extends Controller
{
    protected const MIN_ITEM_WORDS = 100;
    protected const MAX_ITEM_WORDS = 3000;
    protected const MAX_TAG_OVERLAP_PERCENTAGE = 90;

    /**
     * Find items and tags that need attention. Note that not all that is marked as interesting is necessarily wrong.
     *
     * Because this controller endpoint can contain multiple big queries, special attention to performance during the writing of the queries
     * is given. This is done by using raw SQL queries where needed and optimizing the joins and conditions.
     */
    public function __invoke(): View
    {
        $itemsTooShort = $this->itemsTooShort();
        $itemsTooLong = $this->itemsTooLong();
        $itemsTooComplex = $this->itemsTooComplex();
        $itemsMissingCategories = $this->itemsMissingCategories();
        $campsWithoutActivities = $this->campsWithoutActivities();
        $tagsMissingAgeGroups = $this->tagsMissingAgeGroups();
        $tagsHighOverlap = $this->tagsHighOverlap();

        return view('stats.item-control', [
            'minItemWords'           => self::MIN_ITEM_WORDS,
            'itemsTooShort'          => $itemsTooShort,
            'maxItemWords'           => self::MAX_ITEM_WORDS,
            'itemsTooLong'           => $itemsTooLong,
            'itemsTooComplex'        => $itemsTooComplex,
            'itemsMissingCategories' => $itemsMissingCategories,
            'campsWithoutActivities' => $campsWithoutActivities,
            'tagsMissingAgeGroups'   => $tagsMissingAgeGroups,
            'tagsHighOverlap'        => $tagsHighOverlap,
        ]);
    }

    protected function itemsTooShort(): Collection
    {
        return Item::query()
            ->where('word_count', '<', self::MIN_ITEM_WORDS)
            ->orderBy('word_count')
            ->get();
    }

    protected function itemsTooLong(): Collection
    {
        return Item::query()
            ->where('word_count', '>', self::MAX_ITEM_WORDS)
            ->orderByDesc('word_count')
            ->get();
    }

    protected function itemsTooComplex(): Collection
    {
        return Item::query()
            ->where('flesch_reading_ease', '<', '50')
            ->where('word_count', '<', '50') // Remove some potential false positives.
            ->orderBy('flesch_reading_ease')
            ->get();
    }

    /**
     * Find items with missing categories in a required category group.
     */
    protected function itemsMissingCategories(): Collection
    {
        $results = DB::select('
            SELECT items.id, items.title, items.slug, items.hash, category_groups.name as group_name
            FROM items
            CROSS JOIN category_groups
            WHERE category_groups.is_required = 1
              AND items.deleted_at IS NULL
              AND (
                -- Only missing if the type of the Item is matching the type of the CategoryGroup.
                (category_groups.is_available_for_activities = 1 AND items.is_camp = 0)
                OR
                (category_groups.is_available_for_camps = 1 AND items.is_camp = 1)
              )
              AND NOT EXISTS (
                SELECT 1
                FROM category_item
                INNER JOIN categories ON categories.id = category_item.category_id
                WHERE category_item.item_id = items.id
                  AND categories.category_group_id = category_groups.id
              )
            ORDER BY category_groups.name, items.title');

        return collect($results)->groupBy('group_name');
    }

    protected function campsWithoutActivities(): Collection
    {
        return Item::query()
            ->withoutGlobalScope('published')
            ->where('is_camp', true)
            ->whereDoesntHave('activities')
            ->get();
    }

    /**
     * Find tags without items for each age group.
     */
    protected function tagsMissingAgeGroups(): array
    {
        // TODO Also return the names of the missing age groups.
        $ageGroupId = 1; // TODO Maybe generalize for use for some other category groups too?

        return DB::select('
            WITH age_group_count AS (
                SELECT COUNT(*) as total
                FROM categories
                WHERE category_group_id = ?
            )
            SELECT
                tags.name as tag_name,
                (SELECT total FROM age_group_count) - COUNT(DISTINCT categories.id) as missing_age_group_count
            FROM tags
            LEFT JOIN item_tag ON tags.id = item_tag.tag_id
            LEFT JOIN items ON item_tag.item_id = items.id
            LEFT JOIN category_item ON items.id = category_item.item_id
            LEFT JOIN categories ON category_item.category_id = categories.id AND categories.category_group_id = ?
            GROUP BY tags.id, tags.name
            HAVING missing_age_group_count
            ORDER BY tags.name
        ', [$ageGroupId, $ageGroupId]);
    }

    /**
     * Calculate overlap percentage between each pair of tags, and only return pairs above threshold percentage.
     */
    protected function tagsHighOverlap(): array
    {
        return DB::select('
            -- First CTE: Get count of items for each tag.
            WITH tag_counts AS (
                SELECT t.id, t.name, COUNT(it.item_id) as items_count
                FROM tags t
                LEFT JOIN item_tag it ON t.id = it.tag_id
                GROUP BY t.id, t.name
            ),
            -- Second CTE: Calculate overlap percentage between each pair of tags.
            tag_overlaps AS (
                SELECT
                    t1.name as tag1_name,
                    t2.name as tag2_name,
                    (COUNT(*) * 100.0 / LEAST(t1.items_count, t2.items_count)) as overlap_percentage
                FROM tag_counts t1
                JOIN item_tag it1 ON t1.id = it1.tag_id
                JOIN item_tag it2 ON it1.item_id = it2.item_id
                JOIN tag_counts t2 ON t2.id = it2.tag_id
                -- Only compare each pair once (avoid duplicates like A-B and B-A).
                WHERE t1.id < t2.id
                GROUP BY t1.id, t1.name, t1.items_count, t2.id, t2.name, t2.items_count
            )
            SELECT
                tag1_name as tag1,
                tag2_name as tag2,
                ROUND(overlap_percentage, 1) as percentage
            FROM tag_overlaps
            -- Only return pairs above threshold percentage.
            WHERE overlap_percentage > ?
            ORDER BY tag1_name, tag2_name
        ', [self::MAX_TAG_OVERLAP_PERCENTAGE]);
    }
}
