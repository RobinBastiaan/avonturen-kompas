@extends('layouts.app')

@section('main')
    <h1 class="text-3xl font-bold">Dashboard</h1>

    {{-- Links to other stats pages --}}
    <p><a href="{{ route('stats.activities-per-age-group') }}">Activiteiten per leeftijdgroep</a></p>
    <p><a href="{{ route('stats.item-control') }}">Item control</a></p>

    {{-- Card: Most populair @-scout Item --}}
    @if($mostPopulairAtScoutItem)
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <h3 class="text-xl font-bold">Meest populaire @-scout activiteit van afgelopen 6 maanden</h3>
            <div class="inline-block min-w-full py-2 align-middle">
                <a href="{{ route('item', ['hash' => $mostPopulairAtScoutItem->hash, 'slug' => $mostPopulairAtScoutItem->slug]) }}">
                    "{{ $mostPopulairAtScoutItem->title }}"
                </a>
                met {{ $mostPopulairAtScoutItem->hits }} hits
            </div>
        </div>
    @endif
@endsection
