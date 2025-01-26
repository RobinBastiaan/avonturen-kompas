<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Give a dashboard containing some short interesting statistics.
     */
    public function __invoke(): View
    {
        $mostPopulairAtScoutItem = Item::query()
            ->whereHas('atScouts')
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderByDesc('hits')
            ->first();

        $mostFavoriteItem = Item::query()
            ->withCount('favoritedBy')
            ->orderByDesc('favorited_by_count')
            ->first();

        $mostRecentEditedItem = Item::query()
            ->orderByDesc('updated_at')
            ->first();

        $taggedItems = Item::query()->whereHas('tags')->count();
        $totalItems = Item::query()->count();
        $percentageTagged = $taggedItems / $totalItems * 100;

        $randomStaleItems = Item::query()
            ->select('id', 'title', 'slug', 'hash')
            ->where('created_at' , '<', now()->subYears(5))
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('stats.dashboard', compact(
            'mostPopulairAtScoutItem',
            'mostFavoriteItem',
            'mostRecentEditedItem',
            'taggedItems',
            'totalItems',
            'percentageTagged',
            'randomStaleItems',
        ));
    }
}
