<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Avonturen Kompas</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
            <div class="flex flex-col items-center justify-center gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800">
                <div class="flex justify-center w-full">
                    <div class="mt-8 flow-root">
                        @yield('main')
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
