<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Scopes\PublishedScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivitiesPerAgeGroup extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $stats = Item::query()
            ->withoutGlobalScope(PublishedScope::class)
            ->join('category_item as ci1', 'items.id', '=', 'ci1.item_id')
            ->join('categories as c1', 'c1.id', '=', 'ci1.category_id')
            ->join('category_item as ci2', 'items.id', '=', 'ci2.item_id')
            ->join('categories as c2', 'c2.id', '=', 'ci2.category_id')
            ->where('c1.category_group_id', 1) // Age group
            ->where('c2.category_group_id', 2) // Activity area
            ->groupBy('c1.id', 'c1.name', 'c2.id', 'c2.name')
            ->select(
                'c1.name as age_group',
                'c2.name as activity_area',
                \DB::raw('count(*) as count')
            )
            ->get()
            ->groupBy('activity_area')
            ->map(function ($group) {
                return $group->pluck('count', 'age_group');
            });

        return view('stats.activities-per-age-group', compact('stats'));
    }
}
