<div class="my-8">
    <h2 class="text-xl font-bold mb-4">Tags</h2>

    @foreach($tags as $tag)
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3">
                {{ $tag->name }}

                @if($tag->hasSpecialInterest())
                    <span class="ml-2 text-sm text-blue-600">Special Interest</span>
                @endif
            </h3>

            @if($tag->items->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($tag->items as $relatedItem)
                        <a href="{{ route('item', ['hash' => $relatedItem->hash, 'slug' => $relatedItem->slug]) }}"
                           class="p-4 border rounded-lg hover:bg-gray-50">
                            <h4 class="font-semibold">{{ $relatedItem->title }}</h4>
                            <p class="text-sm text-gray-600">{{ Str::limit($relatedItem->summary, 150) }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
