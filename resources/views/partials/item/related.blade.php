<div class="my-8">
    @if($activities->isNotEmpty())
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4">Gerelateerde activiteiten</h2>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($activities as $activity)
                    <a href="{{ route('item', ['hash' => $activity->hash, 'slug' => $activity->slug]) }}"
                       class="p-4 border rounded-lg hover:bg-gray-50">
                        <h3 class="font-semibold">{{ $activity->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $activity->summary }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($camps->isNotEmpty())
        <div class="mb-6">
            <h2 class="text-xl font-bold mb-4">Gerelateerde kampen</h2>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($camps as $camp)
                    <a href="{{ route('item', ['hash' => $camp->hash, 'slug' => $camp->slug]) }}"
                       class="p-4 border rounded-lg hover:bg-gray-50">
                        <h3 class="font-semibold">{{ $camp->title }}</h3>
                        <p class="text-sm text-gray-600">{{ $camp->summary }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
