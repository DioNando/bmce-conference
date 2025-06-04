<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(isset($exception) && $exception instanceOf \Symfony\Component\HttpKernel\Exception\HttpException)
        @if($exception->getStatusCode() == 503)
            <meta name="robots" content="noindex, nofollow">
            <meta http-equiv="refresh" content="60">
        @endif
    @endif

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

<body class="h-full bg-base-200">
    <div class="language-switcher fixed top-4 right-4 z-50">
        <div>
            <a href="{{ route('language.switch', 'fr') }}" class="btn btn-sm font-mono {{ app()->getLocale() == 'fr' ? 'btn-primary' : '' }}">FR</a>
            <a href="{{ route('language.switch', 'en') }}" class="btn btn-sm font-mono {{ app()->getLocale() == 'en' ? 'btn-primary' : '' }}">EN</a>
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="page-loading" class="fixed inset-0 bg-base-100 bg-opacity-80 z-40 items-center justify-center" style="display: none;">
        <div class="loading loading-spinner loading-lg text-primary"></div>
    </div>

    <main>
        {{ $slot }}
    </main>

    <!-- Script for loading animation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loading = document.getElementById('page-loading');

            // Show loading on page navigation
            document.addEventListener('click', function(e) {
                const target = e.target.closest('a');
                if (target && target.getAttribute('href') && !target.getAttribute('href').startsWith('#') && !e.ctrlKey && !e.metaKey) {
                    loading.style.display = 'flex';
                }
            });

            // Hide loading when page is fully loaded
            window.addEventListener('load', function() {
                loading.style.display = 'none';
            });

            // Hide loading when back/forward navigation
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    loading.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
