@props(['title' => null, 'subtitle' => null, 'action' => null, 'dropdown' => false, 'type' => 'default'])
@php
    $typeClass = function () use ($type) {
        return match ($type) {
            'default' => 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100',
            'primary' => 'bg-blue-100 dark:bg-blue-800 border-blue-200 dark:border-blue-700 text-blue-900 dark:text-blue-100',
            'success' => 'bg-green-100 dark:bg-green-800 border-green-200 dark:border-green-700 text-green-900 dark:text-green-100',
            'warning' => 'bg-yellow-100 dark:bg-yellow-800 border-yellow-200 dark:border-yellow-700 text-yellow-900 dark:text-yellow-100',
            'danger' => 'bg-red-100 dark:bg-red-800 border-red-200 dark:border-red-700 text-red-900 dark:text-red-100',
            default => '',
        };
    };
@endphp

<div class="px-6 pt-4 flex items-center gap-4 {{ $typeClass() }} ">
    @if ($dropdown)
    <button @click="open = !open" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
        <x-heroicon-o-chevron-down x-show="open" x-cloak class="size-4" />
        <x-heroicon-o-chevron-right x-show="!open" x-cloak class="size-4" />
    </button>
    @endif
    <div class="flex justify-between items-center">
        <div>
            @if ($title)
                <h3 class="text-lg font-semibold">{{ $title }}</h3>
            @endif

            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
            @endif

            @if ($slot->isNotEmpty())
                {{ $slot }}
            @endif
        </div>
    </div>
</div>
