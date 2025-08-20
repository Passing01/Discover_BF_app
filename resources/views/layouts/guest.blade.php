<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Discover_BF') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen relative flex flex-col items-center justify-center">
            <!-- Background video (muted, autoplay, loop) -->
            <video
                id="authBgVideo"
                class="absolute inset-0 w-full h-full object-cover"
                src="{{ asset('assets/img/Bienvenue_au_Burkina_Faso.mp4') }}"
                autoplay
                muted
                loop
                playsinline
                preload="auto"
            ></video>
            <!-- Gradient overlay for readability -->
            <div class="absolute inset-0" style="background-image: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.55));"></div>

            <!-- Branding header -->
            <div class="relative text-center text-white px-6">
                <a href="/" class="inline-flex items-center gap-3">
                    <x-application-logo class="w-12 h-12 fill-current text-white opacity-90" />
                    <div class="text-left">
                        <div class="text-2xl sm:text-3xl font-semibold">Discover Burkina Faso</div>
                        <div class="text-sm sm:text-base opacity-90">Votre porte d'entrée vers l'Odyssée africaine</div>
                    </div>
                </a>
            </div>

            <!-- Auth card -->
            <div class="relative w-full sm:max-w-md mt-6 px-6 py-6 bg-white/95 backdrop-blur shadow-xl overflow-hidden sm:rounded-xl">
                {{ $slot }}
            </div>

            <!-- Footer note -->
            <div class="relative mt-6 text-white/90 text-xs sm:text-sm px-6 text-center">
                <span>Conseil voyage: explorez Ouaga, Bobo-Dioulasso et le Pays Lobi — traditions, festivals et hospitalité authentique.</span>
            </div>
        </div>
    </body>
</html>
