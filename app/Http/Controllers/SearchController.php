<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\View\View;

class SearchController extends Controller
{

    /**
     * View a list of Items based on your search criteria.
     */
    public function __invoke(): View
    {
        // TODO Build real dynamic query to replace this placeholder.
        $searchResults = Item::query()->limit(3)->get();

        return view('search', compact('searchResults'));
    }
}
