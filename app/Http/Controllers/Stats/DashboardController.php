<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $mostPopulairAtScoutItem = Item::query()
            ->whereHas('atScouts')
            ->where('created_at', '>=', now()->subMonths(6))
            ->orderByDesc('hits')
            ->first();

        return view('stats.dashboard', compact('mostPopulairAtScoutItem'));
    }
}
