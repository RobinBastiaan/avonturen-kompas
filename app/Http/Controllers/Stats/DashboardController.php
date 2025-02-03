<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public const LIST_LIMIT = 3;

    /**
     * Give a dashboard containing some short interesting statistics.
     */
    public function __invoke(): View
    {
        // It is policy to only use newly written items as an @-scout item.
        $mostPopularAtScoutItem = Item::query()
            ->whereHas('atScouts')
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderByDesc('hits')
            ->first();

        $mostFavoriteItem = Item::query()
            ->withCount('favoritedBy')
            ->orderByDesc('favorited_by_count')
            ->first();

        // It only makes sense to show the most recent edited item when in production, because it is read-only otherwise.
        $mostRecentEditedItem = app()->isProduction()
            ? Item::query()->orderByDesc('updated_at')->first()
            : null;

        $taggedItems = Item::query()->whereHas('tags')->count();
        $totalItems = Item::query()->count();
        $percentageTagged = $taggedItems / $totalItems * 100;

        $randomStaleItems = Item::query()
            ->select('id', 'title', 'slug', 'hash')
            ->where('created_at', '<', now()->subYears(5))
            ->inRandomOrder()
            ->limit(self::LIST_LIMIT)
            ->get();

        $mostPopularItemsLastMonth = $this->getMostPopularItemsLastMonth();

        return view('stats.dashboard', compact(
            'mostPopularAtScoutItem',
            'mostFavoriteItem',
            'mostRecentEditedItem',
            'taggedItems',
            'totalItems',
            'percentageTagged',
            'randomStaleItems',
            'mostPopularItemsLastMonth',
        ));
    }

    protected function getMostPopularItemsLastMonth(): Collection
    {
        return Item::query()
            ->select('id', 'title', 'slug', 'hash', 'hits')
            ->whereHas('historicalHits', function ($query) {
                $query->where('extracted_at', '>=', now()->subMonth());
            })
            ->withSum(['historicalHits as current_month_hits' => function ($query) {
                $query->where('extracted_at', '>=', now()->startOfMonth());
            }], 'hits')
            ->withSum(['historicalHits as last_month_hits' => function ($query) {
                $query->whereBetween('extracted_at', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth(),
                ]);
            }], 'hits')
            ->orderByRaw('current_month_hits - last_month_hits DESC')
            ->limit(self::LIST_LIMIT)
            ->get()
            ->each(function ($item) {
                $item->hits_diff = $item->current_month_hits - $item->last_month_hits;
            });
    }
}
