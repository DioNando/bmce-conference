@php
    $types = [
        'success' => [
            'bg' => 'bg-green-600',
            'icon' => 'heroicon-o-check-circle',
            'ring' => 'ring-green-500',
            'position' => 'top-right',
            'duration' => 4000,
        ],
        'error' => [
            'bg' => 'bg-red-600',
            'icon' => 'heroicon-o-x-circle',
            'ring' => 'ring-red-500',
            'position' => 'top-right',
            'duration' => 6000,
        ],
        'warning' => [
            'bg' => 'bg-yellow-600',
            'icon' => 'heroicon-o-exclamation-triangle',
            'ring' => 'ring-yellow-500',
            'position' => 'top-left',
            'duration' => 5000,
        ],
        'info' => [
            'bg' => 'bg-blue-600',
            'icon' => 'heroicon-o-information-circle',
            'ring' => 'ring-blue-500',
            'position' => 'top-right',
            'duration' => 4000,
        ],
        'primary' => [
            'bg' => 'bg-indigo-600',
            'icon' => 'heroicon-o-star',
            'ring' => 'ring-indigo-500',
            'position' => 'bottom-center',
            'duration' => 4000,
        ],
        'secondary' => [
            'bg' => 'bg-gray-600',
            'icon' => 'heroicon-o-bell',
            'ring' => 'ring-gray-500',
            'position' => 'bottom-left',
            'duration' => 4000,
        ],
    ];

    $positions = [
        'top-right' => 'sm:items-end',
        'top-center' => 'sm:items-center',
        'top-left' => 'sm:items-start',
        'bottom-right' => 'sm:items-end sm:items-end',
        'bottom-center' => 'sm:items-center sm:items-end',
        'bottom-left' => 'sm:items-start sm:items-end',
    ];
@endphp

@foreach ($types as $type => $config)
    @if (session()->has($type))
        <div x-data="{ open: true }"
             x-init="setTimeout(() => open = false, {{ $config['duration'] }})"
             x-show="open"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             aria-live="assertive"
             class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50">

            <div class="flex w-full flex-col items-center space-y-4 {{ $positions[$config['position']] }}">
                <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg {{ $config['bg'] }} text-white shadow-lg ring-1 ring-black/5">
                    <div class="p-4 flex items-start">
                        <div class="shrink-0">
                            <x-dynamic-component :component="$config['icon']" class="size-7" />
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium">{{ session($type) }}</p>
                            @if (session()->has($type . '_description'))
                                <p class="mt-1 text-sm opacity-90">{{ session($type . '_description') }}</p>
                            @endif
                        </div>
                        <div class="ml-4 flex shrink-0">
                            <button @click="open = false" type="button"
                                class="inline-flex rounded-md hover:text-gray-200 focus:ring-2 focus:{{ $config['ring'] }} focus:ring-offset-2 focus:outline-none">
                                <span class="sr-only">Fermer</span>
                                <x-heroicon-o-x-mark class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
