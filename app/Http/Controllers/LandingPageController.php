<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public const LIST_LIMIT = 5;

    /**
     * First page of the application.
     */
    public function __invoke(): View
    {
        $latestAdditions = $this->getLatestAdditions();
        $randomActivities = $this->getRandomActivities();
        $tagTips = $this->getTagTips();

        return view('landing-page', compact('latestAdditions', 'randomActivities', 'tagTips'));
    }

    /**
     * @return Collection|Item[]
     */
    protected function getLatestAdditions(): Collection
    {
        return Cache::rememberForever('latest_additions', function () {
            return Item::query()
                ->select(['id', 'hash', 'slug', 'title', 'summary'])
                ->latest()
                ->take(self::LIST_LIMIT)
                ->get();
        });
    }

    /**
     * @return Collection|Item[]
     */
    protected function getRandomActivities(): Collection
    {
        return Item::query()
            ->select(['id', 'hash', 'slug', 'title', 'summary'])
            ->inRandomOrder()
            ->limit(self::LIST_LIMIT)
            ->get();
    }

    protected function getTagTips(): Collection
    {
        $freshSeconds = 60 * 30; // Half hour.
        $staleSeconds = 60 * 60; // 1 hour.

        return Cache::flexible('tag_tips', [$freshSeconds, $staleSeconds], function () {
            $randomTag = $this->getRandomTag();

            return collect(['tag' => $randomTag, 'items' => Item::query()
                ->select(['id', 'hash', 'slug', 'title', 'summary'])
                ->whereHas('tags', function ($query) use ($randomTag) {
                    $query->where('id', $randomTag->id);
                })
                ->take(self::LIST_LIMIT)
                ->get()]);
        });
    }

    /**
     * Select a random Tag while preferring upcoming Tags and selecting them in equal probability year round.
     */
    protected function getRandomTag(): Tag
    {
        $monthsInAdvance = 3;
        $upcomingTags = Tag::has('items', '>=', self::LIST_LIMIT)->withUpcomingSpecialInterest($monthsInAdvance)->get();
        $timelessTags = Tag::has('items', '>=', self::LIST_LIMIT)->whereNull('special_interest_at')->get();

        $repeatedUpcomingTags = collect();
        $repeatTimes = ceil(12 / $monthsInAdvance);

        for ($i = 0; $i < $repeatTimes; $i++) {
            $repeatedUpcomingTags = $repeatedUpcomingTags->merge($upcomingTags);
        }

        return collect()
            ->merge($repeatedUpcomingTags)
            ->merge($timelessTags)
            ->random();
    }
}
