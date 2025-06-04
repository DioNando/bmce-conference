@props(['type' => 'info', 'dismissable' => true, 'icon' => true])

@php
    $types = [
        'info' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/30',
            'text' => 'text-blue-800 dark:text-blue-300',
            'icon' => 'heroicon-o-information-circle',
            'border' => 'border-blue-300 dark:border-blue-700',
            'ring' => 'ring-blue-500 dark:ring-blue-600'
        ],
        'success' => [
            'bg' => 'bg-green-50 dark:bg-green-900/30',
            'text' => 'text-green-800 dark:text-green-300',
            'icon' => 'heroicon-o-check-circle',
            'border' => 'border-green-300 dark:border-green-700',
            'ring' => 'ring-green-500 dark:ring-green-600'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50 dark:bg-yellow-900/30',
            'text' => 'text-yellow-800 dark:text-yellow-300',
            'icon' => 'heroicon-o-exclamation-triangle',
            'border' => 'border-yellow-300 dark:border-yellow-700',
            'ring' => 'ring-yellow-500 dark:ring-yellow-600'
        ],
        'error' => [
            'bg' => 'bg-red-50 dark:bg-red-900/30',
            'text' => 'text-red-800 dark:text-red-300',
            'icon' => 'heroicon-o-x-circle',
            'border' => 'border-red-300 dark:border-red-700',
            'ring' => 'ring-red-500 dark:ring-red-600'
        ],
    ];

    $config = $types[$type] ?? $types['info'];
@endphp

<div x-data="{ open: true }"
    x-show="open"
    {{ $attributes->merge(['class' => "{$config['bg']} border {$config['border']} rounded-md p-4"]) }}
    role="alert">
    <div class="flex">
        @if($icon)
        <div class="flex-shrink-0">
            <x-dynamic-component :component="$config['icon']" class="h-5 w-5 {{ $config['text'] }}" />
        </div>
        @endif
        <div class="ml-3 flex-1 {{ $config['text'] }}">
            <div class="text-sm font-medium">
                {{ $slot }}
            </div>
        </div>
        @if($dismissable)
        <div class="pl-3 ml-auto">
            <div class="-mx-1.5 -my-1.5">
                <button @click="open = false" type="button" class="{{ $config['text'] }} rounded-md p-1.5 hover:bg-white dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:{{ $config['ring'] }}">
                    <span class="sr-only">Fermer</span>
                    <x-heroicon-o-x-mark class="h-4 w-4" />
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
