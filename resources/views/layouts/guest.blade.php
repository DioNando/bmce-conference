<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/sidebar.css', 'resources/scss/app.scss', 'resources/js/app.js', 'resources/css/jsvectormap.min.css'])
    @livewireStyles
    @livewireScripts
    @stack('scripts')
    @stack('styles')
</head>

<body class="h-full flex flex-col lg:flex-row items-center">
    <div class="flex-3 order-2 lg:order-1 p-8">
        <x-countdown />
    </div>
    <div class="flex-2 p-8 order-1 lg:order-2 flex flex-col justify-center items-center">
        <!-- Language Switcher -->
        <div class="absolute top-4 right-4">
            <div>
                <a href="{{ route('language.switch', 'fr') }}"
                    class="btn btn-sm font-mono rounded-full {{ app()->getLocale() == 'fr' ? 'btn-primary' : '' }}">FR</a>
                <a href="{{ route('language.switch', 'en') }}"
                    class="btn btn-sm font-mono rounded-full {{ app()->getLocale() == 'en' ? 'btn-primary' : '' }}">EN</a>
            </div>
        </div>

        <a href="/" class="mb-4">
            <x-application-logo class="h-20 fill-current text-gray-500" />
        </a>

        <div>
            {{ $slot }}
        </div>
    </div>
</body>

</html>
