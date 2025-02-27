<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Avonturen Kompas</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="text-[#1b1b18] min-h-screen">
        <header class="w-full max-w-7xl mx-auto px-6 py-6">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-6">
                    {{-- TODO Fix links --}}
                    <a href="{{ route('search') }}" class="text-l text-[#F2940A]">Zoeken</a>
                    <a href="{{ url('/') }}" class="text-l text-[#F2940A]">Toevoegen</a>
                    <a href="{{ url('/') }}" class="text-l text-[#F2940A]">Insignes</a>
                    <a href="{{ url('/') }}" class="text-l text-[#F2940A]">Last Minute Box</a>
                    <a href="{{ url('/') }}" class="text-l text-[#F2940A]">Speluitleg</a>
                    <a href="{{ url('/') }}" class="text-l text-[#F2940A]">Uitdagingen bij Scouting</a>
                    <a href="{{ url('/') }}" class="text-l text-[#F2940A]">Werken in Subgroepen</a>
                    @auth
                        <a href="{{ url('/stats') }}" class="text-l text-[#F2940A]">Stats</a>
                    @else
                        <a href="{{ route('login') }}" class="text-l text-[#F2940A]">Inloggen</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-l text-[#F2940A]">Registreren</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="relative bg-[#FDFDFC] dark:bg-[#0a0a0a]">
            <!-- Hero Image Section -->
            <div class="relative h-[40vh] w-full">
                <img src="{{ asset('images/home-hero.jpg') }}" class="w-full h-full object-cover">

                <!-- Content -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white">
                    <h1 class="text-4xl md:text-6xl font-bold mb-8 font-['UrbanJungle']">Avonturen &nbsp; Kompas</h1>
                    <form action="{{ route('search') }}" method="GET" class="flex gap-2">
                        <input
                            type="text"
                            name="q"
                            placeholder="Zoek activiteiten..."
                            class="bg-white/90 text-black px-4 py-3 rounded-[50px] text-xl w-80 placeholder:text-gray-500"
                        >
                        <button
                            type="submit"
                            class="bg-[#0066b2] border border-[#0066b2] text-white rounded-[50px] text-base px-[30px] py-[12px] uppercase font-semibold leading-[1.2em] shadow-[0_2px_4px_0_rgba(0,0,0,0.2)] transition-all duration-200 hover:bg-[#005291] hover:border-[#005291]"
                        >
                            Zoek
                        </button>
                    </form>
                </div>
            </div>

            <!-- Activities Section -->
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Latest Additions -->
                    <div class="bg-white dark:bg-[#1b1b18] rounded-lg shadow-sm border border-[#19140015] dark:border-[#3E3E3A] p-8">
                        <h2 class="text-2xl font-semibold mb-6 text-[#0066B2] dark:text-[#EDEDEC] font-['Roboto_Slab',serif]">Laatste 5 toevoegingen</h2>

                        <ul class="space-y-4">
                            @foreach($latestAdditions as $item)
                                <li class="group">
                                    <a href="{{ route('item', ['hash' => $item->hash, 'slug' => $item->slug]) }}">
                                        <h3 class="text-l font-semibold text-[#F2940A]">{{ $item->title }}</h3>
                                        <p><small>{{ Str::limit($item->summary, 100) }}</small></p>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Random Activities -->
                    <div class="bg-white dark:bg-[#1b1b18] rounded-lg shadow-sm border border-[#19140015] dark:border-[#3E3E3A] p-8">
                        <h2 class="text-2xl font-semibold mb-6 text-[#0066B2] dark:text-[#EDEDEC] font-['Roboto_Slab',serif]">Willekeurige activiteit</h2>

                        <ul class="space-y-4">
                            @foreach($randomActivities as $item)
                                <li class="group">
                                    <a href="{{ route('item', ['hash' => $item->hash, 'slug' => $item->slug]) }}">
                                        <h3 class="text-l font-semibold text-[#F2940A]">{{ $item->title }}</h3>
                                        <p><small>{{ Str::limit($item->summary, 100) }}</small></p>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Tag Tips -->
                    <div class="bg-white dark:bg-[#1b1b18] rounded-lg shadow-sm border border-[#19140015] dark:border-[#3E3E3A] p-8">
                        <h2 class="text-2xl font-semibold mb-6 text-[#0066B2] dark:text-[#EDEDEC] font-['Roboto_Slab',serif]">Tag Tip: {{ $tagTips['tag']->name }}</h2>

                        <ul class="space-y-4">
                            @foreach($tagTips['items'] as $item)
                                <li class="group">
                                    <a href="{{ route('item', ['hash' => $item->hash, 'slug' => $item->slug]) }}">
                                        <h3 class="text-l font-semibold text-[#F2940A]">{{ $item->title }}</h3>
                                        <p><small>{{ Str::limit($item->summary, 100) }}</small></p>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="max-w-7xl mx-auto px-6">
                <div class="w-full mt-[10px] mb-[20px] border-t-4 border-dotted border-[#0066B2] dark:border-[#00a551]"></div>
            </div>

            <!-- Text Section -->
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="grid md:grid-cols-1 gap-8">
                    <div class="bg-white dark:bg-[#1b1b18] rounded-lg shadow-sm border border-[#19140015] dark:border-[#3E3E3A] p-8">
                        <h2 class="text-2xl font-semibold mb-6 text-[#0066B2] dark:text-[#EDEDEC] font-['Roboto_Slab',serif]">Kom je met ons spelstormen?</h2>

                        <p>
                            Vind je het leuk om spelideeën uit te wisselen met andere creatievelingen uit het land?
                            Doe dan met ons mee! Landelijk team Spel organiseert op <strong>13 april</strong> een Spelstorm van <strong>10:00 uur tot 16:00</strong> uur
                            bij Scouting nederland op <strong>Nulderpad 1, Zeewolde</strong>.
                            Tevens wordt er een heerlijke maaltijd geregeld door de chefkoks van Team Spel.
                        </p>

                        <div class="flex justify-center mt-[20px]">
                            <a
                                href="https://forms.office.com/pages/responsepage.aspx?id=vJfnyAXwjEe04ortFaLfyEFJ9f6_ouRNmcthKzEbSLVUQk1DRjM5RlFKMTZCNjk3ODFVSTA1T0Y0TSQlQCN0PWcu&route=shorturl"
                                class="bg-[#0066b2] border border-[#0066b2] text-white rounded-[50px] text-base px-[30px] py-[12px] uppercase font-semibold leading-[1.2em] shadow-[0_2px_4px_0_rgba(0,0,0,0.2)] transition-all duration-200 hover:bg-[#005291] hover:border-[#005291]"
                            >
                                Schrijf je nu in!
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
