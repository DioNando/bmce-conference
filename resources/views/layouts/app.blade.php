<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Prevent sidebar flash on page load -->
    <script>
        // Apply sidebar state immediately before page renders
        (function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            // Add a class to the html element to control initial rendering
            document.documentElement.classList.add(sidebarCollapsed ? 'sidebar-collapsed' : 'sidebar-expanded');
        })();
    </script>

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

<body id="app" x-data="{ open: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' }" x-init="$watch('sidebarCollapsed', val => { localStorage.setItem('sidebarCollapsed', val);
    document.documentElement.classList.toggle('sidebar-collapsed', val);
    document.documentElement.classList.toggle('sidebar-expanded', !val); })" class="h-full text-base-content">
    <x-layout.sidebar />
    <!-- Page Content -->
    <main id="content" class="transition-all duration-200 lg:pl-64 sm:pl-0">
        @include('layouts.navigation')
        <!-- Page Heading -->
        @if (isset($header))
            <header class="px-2 pt-2 lg:pt-8 lg:px-8">
                {{ $header }}
            </header>
        @endif
        <!-- Page Content -->
        <div class="p-2 lg:p-8">
            {{ $slot }}
        </div>
    </main>
</body>

</html>
