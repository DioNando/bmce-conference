@props(['icon' => '', 'label', 'route' => null])

<div class="flex items-center gap-4">
    <a class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 border-r-2 pr-4 border-gray-400 dark:border-gray-500"
       href="{{ $route ? route($route) : 'javascript:history.back()' }}">
        <x-heroicon-o-chevron-left class="size-5 shrink-0" aria-hidden="true" />
        <span class="sr-only">Retour</span> Retour
    </a>
    <h3 class="flex items-center gap-2 text-2xl font-bold text-blue-600 dark:text-blue-400">
        @if ($icon)
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="size-6 shrink-0" />
        @endif
        {{ $label }}
    </h3>
</div>


{{-- <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
    <a class="flex items-center gap-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors duration-200"
       href="{{ $route ? route($route) : 'javascript:history.back()' }}">
        <x-heroicon-o-chevron-left class="size-4 shrink-0" aria-hidden="true" />
        <span>Retour</span>
    </a>

    <div class="flex-grow">
        <h3 class="inline-flex items-center px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-sm">
            @if ($icon)
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="size-6 shrink-0 mr-2" />
            @endif
            <span class="font-semibold text-lg">{{ $label }}</span>
        </h3>
    </div>
</div> --}}
