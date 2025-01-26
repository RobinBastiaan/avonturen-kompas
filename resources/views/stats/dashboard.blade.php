@extends('layouts.app')

@section('main')
    <h1 class="text-3xl font-bold">Dashboard</h1>

    {{-- Links to other stats pages --}}
    <p><a href="{{ route('stats.activities-per-age-group') }}">Activiteiten per leeftijdgroep</a></p>
    <p><a href="{{ route('stats.item-control') }}">Item control</a></p>

    {{-- Card: Most populair @-scout Item --}}
    @if(isset($mostPopulairAtScoutItem))
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <h3 class="text-xl font-bold">Meest populaire @-scout activiteit van afgelopen 6 maanden</h3>
            <div class="inline-block min-w-full py-2 align-middle">
                <a href="{{ route('item', ['hash' => $mostPopulairAtScoutItem->hash, 'slug' => $mostPopulairAtScoutItem->slug]) }}">
                    {{ $mostPopulairAtScoutItem->title }}
                </a>
                met {{ $mostPopulairAtScoutItem->hits }} hits
            </div>
        </div>
    @endif

    {{-- Card: Most Favorite Item --}}
    @if(isset($mostFavoriteItem) && $mostFavoriteItem->favorited_by_count > 0)
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <h3 class="text-xl font-bold">Meest favoriete activiteit</h3>
            <div class="inline-block min-w-full py-2 align-middle">
                <a href="{{ route('item', ['hash' => $mostFavoriteItem->hash, 'slug' => $mostFavoriteItem->slug]) }}">
                    {{ $mostFavoriteItem->title }}
                </a>
                ({{ $mostFavoriteItem->favorited_by_count }} keer favoriet)
            </div>
        </div>
    @endif

    {{-- Card: Most Recent Edited Item --}}
    @if(isset($mostRecentEditedItem))
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <h3 class="text-xl font-bold">Meest recent aangepaste activiteit</h3>
            <div class="inline-block min-w-full py-2 align-middle">
                <a href="{{ route('item', ['hash' => $mostRecentEditedItem->hash, 'slug' => $mostRecentEditedItem->slug]) }}">
                    {{ $mostRecentEditedItem->title }}
                </a>

                @if($mostFavoriteItem->updatedBy?->name)
                    Aangepast door {{ $mostFavoriteItem->updatedBy->name }}
                @endif
            </div>
        </div>
    @endif

    {{-- Card: Percentage Tagged --}}
    @if(isset($taggedItems) && $taggedItems > 0)
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <h3 class="text-xl font-bold">Percentage Getagd</h3>
            <div class="inline-block min-w-full py-2 align-middle">
                {{ round($percentageTagged) }}%
                ({{ $taggedItems }} van de {{ $totalItems }} items)
            </div>
        </div>
    @endif

    {{-- Random Stale Items --}}
    @if(isset($randomStaleItems) && $randomStaleItems->count() > 0)
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <h3 class="text-xl font-bold">Willekeurig niet-recent aangepaste activiteiten</h3>
            <div class="inline-block min-w-full py-2 align-middle">
                <ul>
                    @foreach($randomStaleItems as $randomStaleItem)
                        <li><a href="{{ route('item', ['hash' => $randomStaleItem->hash, 'slug' => $randomStaleItem->slug]) }}">
                            {{ $randomStaleItem->title }}
                        </a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@endsection
