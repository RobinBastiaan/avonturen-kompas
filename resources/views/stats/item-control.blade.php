@extends('layouts.app')

@section('main')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold mb-8">Item Control</h1>

        {{-- Items Too Short --}}
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Items met te weinig worden (minder dan {{ $minItemWords }} woorden) ({{ $itemsTooShort->count() }} items)</h2>
            @if($itemsTooShort->isNotEmpty())
                <ul class="list-disc pl-5">
                    @foreach($itemsTooShort as $item)
                        <li>
                            <a href="{{ route('item', ['hash' => $item->hash, 'slug' => $item->slug]) }}" class="text-blue-600 hover:underline">
                                {{ $item->title }} ({{ $item->word_count }} woorden)
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">Er zijn geen items met te weinig woorden.</p>
            @endif
        </section>

        <hr>

        {{-- Items Too Long --}}
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Items met te veel worden (meer dan {{ $maxItemWords }} woorden) ({{ $itemsTooLong->count() }} items)</h2>
            @if($itemsTooLong->isNotEmpty())
                <ul class="list-disc pl-5">
                    @foreach($itemsTooLong as $item)
                        <li>
                            <a href="{{ route('item', ['hash' => $item->hash, 'slug' => $item->slug]) }}" class="text-blue-600 hover:underline">
                                {{ $item->title }} ({{ $item->word_count }} woorden)
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">Er zijn geen items met te veel woorden.</p>
            @endif
        </section>

        <hr>

        {{-- Items Too Complex --}}
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Activiteiten met te complexe tekst ({{ $itemsTooComplex->count() }} items)</h2>
            <p>Gebruik kortere zinnen met simpelere woorden en voeg meer interpunctie toe om de leesbaarheid te vergroten.</p>
            @if($itemsTooComplex->isNotEmpty())
                <ul class="list-disc pl-5">
                    @foreach($itemsTooComplex as $item)
                        <li>
                            <a href="{{ route('item', ['hash' => $item->hash, 'slug' => $item->slug]) }}" class="text-blue-600 hover:underline">
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">Er zijn geen items met te complexe tekst.</p>
            @endif
        </section>

        <hr>

        {{-- Items Missing Required Categories --}}
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Items met missende categorieën</h2>
            @forelse($itemsMissingCategories as $categoryGroup => $items)
                @php /** @var \App\Models\Item $item */ @endphp
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2">Missende {{ $categoryGroup }} ({{ $items->count() }} items)</h3>
                    <ul class="list-disc pl-5">
                        @foreach($items as $item)
                            <li>
                                <a href="{{ route('item', ['hash' => $item->hash, 'slug' => $item->slug]) }}" class="text-blue-600 hover:underline">
                                    {{ $item->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-gray-600">Er zijn geen items waarin de vereiste categorieën ontbreken.</p>
            @endforelse
        </section>

        <hr>

        {{-- Camps Without Activities --}}
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Kampen zonder activiteiten ({{ $campsWithoutActivities->count() }} kampen)</h2>
            @if($campsWithoutActivities->isNotEmpty())
                <ul class="list-disc pl-5">
                    @foreach($campsWithoutActivities as $camp)
                        <li>
                            <a href="{{ route('item', ['hash' => $camp->hash, 'slug' => $camp->slug]) }}" class="text-blue-600 hover:underline">
                                {{ $camp->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">Er zijn geen kampen zonder gekoppelde activiteiten.</p>
            @endif
        </section>

        <hr>

        {{-- Broken URL's --}}
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Kapotte URL's ({{ $brokenUrls->count() }} URL's)</h2>
            @if($brokenUrls->isNotEmpty())
                <ul class="list-disc pl-5">
                    @foreach($brokenUrls as $brokenUrl)
                        <li>
                            <a href="{{ route('item', ['hash' => $brokenUrl['item_hash'], 'slug' => $brokenUrl['item_slug']]) }}" class="text-blue-600 hover:underline">
                                {{ $brokenUrl['item_title'] }}
                            </a>
                            <code class="text-sm text-gray-600">
                                {{ Str::limit(Str::before($brokenUrl['url'], '?'), 150) }}
                            </code>
                            <span class="text-sm text-red-600">({{ $brokenUrl['status_code'] }})</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">Er zijn geen kapotte URL's.</p>
            @endif
        </section>

        <hr>

        {{-- Tag Analysis --}}
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Tag Analysis</h2>

            {{-- Tags Missing Age Groups --}}
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Tags met missende leeftijdsgroepen</h3>
                @if(!empty($tagsMissingAgeGroups))
                    <ul class="list-disc pl-5">
                        @foreach($tagsMissingAgeGroups as $data)
                            <li>{{ $data->tag_name }} (mist {{ $data->missing_age_group_count }} leeftijdsgroepen)</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">Geen tags met missende leeftijdsgroepen.</p>
                @endif
            </div>

            {{-- Tags with High Overlap --}}
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-2">Tags met hoge overlap</h3>
                @if(!empty($tagsHighOverlap))
                    <ul class="list-disc pl-5">
                        @foreach($tagsHighOverlap as $data)
                            <li>{{ $data->tag1 }} - {{ $data->tag2 }} ({{ $data->percentage }}% overlap)</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">Geen tags met hoge overlap.</p>
                @endif
            </div>
        </section>
    </div>
@endsection
