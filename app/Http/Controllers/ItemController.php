<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemController extends Controller
{
    const int NUMBER_OF_ITEMS_BY_TAG = 5;

    /**
     * Handle the incoming request.
     */
    public function __invoke(string $hash, ?string $slug = null): View|RedirectResponse
    {
        // When no slug is given, redirect to a URL with that slug for readability and SEO.
        if ($slug === null) {
            $item = Item::query()->select('slug')->where('hash', $hash)->first();

            // If slug remains null, maybe only the slug was given. Try to find an exact match on the slug instead.
            if ($item === null) {
                $item = Item::query()->select('slug', 'hash')->where('slug', $hash)->first();
            }

            if ($item === null) {
                abort(404);
            }

            $slug = $item->slug;
            $hash = $item->hash;

            return redirect()->route('item', ['hash' => $hash, 'slug' => $slug]);
        }

        $item = $this->getItem($hash);

        return view('item', compact('item'));
    }

    protected function getItem(string $hash): Item
    {
        // TODO Cache this query.
        $item = Item::query()
            ->with(['createdBy' => fn($query) => $query->select('id', 'name')])
            ->with(['camps' => function ($query) {
                $query->select('title', 'slug', 'hash', 'summary');
            }])
            ->with(['activities' => function ($query) {
                $query->select('title', 'slug', 'hash', 'summary');
            }])
            ->with(['categories' => function ($query) {
                $query->select('name', 'description', 'category_group_id')
                    ->with(['categoryGroup' => fn($query) => $query->select('id', 'name', 'description')]);
            }])
            ->with(['tags' => function ($query) {
                $query->select('id', 'name', 'description', 'special_interest_at')
                    ->with(['items' => function ($query) {
                        $query->select('items.id', 'items.title', 'items.slug', 'items.hash', 'items.summary')
                            ->inRandomOrder()
                            ->limit(self::NUMBER_OF_ITEMS_BY_TAG);
                    }]);
            }])
            ->with(['comments' => function ($query) {
                $query->select('id', 'item_id', 'comment_id', 'text', 'created_by', 'created_at', 'updated_at')
                    ->with(['createdBy' => fn($query) => $query->select('id', 'name')])
                    ->with(['replies' => function ($query) {
                        $query->select('comment_id', 'text', 'created_by', 'created_at', 'updated_at')
                            ->with(['createdBy' => fn($query) => $query->select('id', 'name')]);
                    }])
                    ->whereNull('comment_id');
            }])
            ->withCount('favoritedBy')
            ->where('hash', $hash)
            ->first();

        if ($item === null) {
            abort(404);
        }

        // Group related categories by their category group.
        $item->grouped_categories = $item->categories->groupBy(function (Category $category) {
            return $category->categoryGroup->name;
        })->map(function ($categories) {
            return $categories->map(function (Category $category) {
                return [
                    'name'        => $category->name,
                    'description' => $category->description,
                ];
            });
        });
        unset($item->categories);

        return $item;
    }
}
