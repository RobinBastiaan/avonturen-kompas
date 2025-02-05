@extends('layouts.app')

@section('main')
    <article class="max-w-4xl mx-auto px-4 py-8">
        {{-- Heading --}}
        <header class="mb-8">
            <h1 class="text-3xl font-bold mb-4">{{ $item->title }}</h1>

            <div class="flex items-center text-gray-600 text-sm">
                <span>
                    @if ($item->createdBy)
                        Door {{ $item->createdBy->name }}
                    @endif
                    @if ($item->created_at)
                        op {{ $item->created_at->translatedFormat('d M Y') }}
                    @endif
                </span>

                @if ($item->favorited_by_count !== 0)
                    <span class="mx-2">•</span>
                    <span>{{ $item->favorited_by_count }} keer favoriet</span>
                @endif
            </div>
        </header>

        {{-- Categories --}}
        @if($item->grouped_categories)
            @include('partials.item.categories', ['categories' => $item->grouped_categories])
        @endif

        {{-- Main Content --}}
        <div class="prose max-w-none my-8">
            <h2 class="text-xl font-bold mb-4">Waarom / doel van de activiteit</h2>
            {{ $item->summary }}

            <h2>Beschrijving van de activiteit</h2>
            {!! $item->description !!}

            @if ($item->requirements)
                <h2 class="text-xl font-bold mb-4">Benodigd materiaal</h2>
                {!! $item->requirements !!}
            @endif

            @if ($item->tips)
                <h2 class="text-xl font-bold mb-4">Tips</h2>
                {!! $item->tips !!}
            @endif

            @if ($item->safety)
                <h2 class="text-xl font-bold mb-4">Veiligheid</h2>
                {!! $item->safety !!}
            @endif
        </div>

        {{-- Camps & Activities --}}
        @if($item->activities->isNotEmpty() || $item->camps->isNotEmpty())
            @include('partials.item.related', [
                'activities' => $item->activities,
                'camps' => $item->camps
            ])
        @endif

        {{-- Tags and related items --}}
        @if($item->tags->isNotEmpty())
            @include('partials.item.tags', ['tags' => $item->tags])
        @endif

        {{-- Comments --}}
        @if($item->comments->isNotEmpty())
            @include('partials.item.comments', ['comments' => $item->comments])
        @endif

        {{-- @-scout publications --}}
        @if($item->atScouts->isNotEmpty())
            <div class="mt-6">
                <h2 class="text-xl font-semibold">Gezien in @-scout</h2>
                <div class="space-y-2 mt-2">
                    @foreach($item->atScouts as $atScout)
                        <div class="flex items-center gap-2">
                            <time datetime="{{ $atScout->published_at->format('Y-m-d') }}" class="text-gray-600">
                                {{ $atScout->published_at->translatedFormat('d M Y') }}
                            </time>
                            <span class="text-gray-600">•</span>
                            <span>{{ $atScout->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </article>

    {{-- Stats --}}
    <div class="mt-6">
        <h2 class="text-xl font-semibold">Statistieken</h2>
        <div class="mt-2 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Datum</th>
                        <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Aantal hits</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($item->historicalHits as $hit)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-600">
                                {{ $hit->extracted_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600 text-right">
                                {{ number_format($hit->hits) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
