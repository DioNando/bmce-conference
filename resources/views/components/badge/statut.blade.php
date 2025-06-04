@props([
    'statut' => null,
    'style' => 'dot', // Options: 'badge', 'dot', 'switch', 'pill', 'card'
])

@php
    $statusMap = [
        1 => [
            'text' => __('Actif'),
            'badge' => 'bg-green-100 text-green-800 ring-green-500/50 dark:bg-green-900/20 dark:text-green-300 dark:ring-green-500/20',
            'dot' => 'before:bg-green-500',
            'switch' => 'bg-green-500',
            'pill' => 'bg-green-500 border-green-600',
            'card' => 'border-l-4 border-l-green-500 bg-green-50 dark:bg-green-900/20',
        ],
        0 => [
            'text' => __('Inactif'),
            'badge' => 'bg-gray-100 text-gray-800 ring-gray-500/50 dark:bg-gray-800/30 dark:text-gray-300 dark:ring-gray-600/20',
            'dot' => 'before:bg-gray-400',
            'switch' => 'bg-gray-300',
            'pill' => 'bg-gray-300 border-gray-400',
            'card' => 'border-l-4 border-l-gray-400 bg-gray-50 dark:bg-gray-800/30',
        ],
        'default' => [
            'text' => __('Inconnu'),
            'badge' => 'bg-amber-100 text-amber-800 ring-amber-500/50 dark:bg-amber-900/20 dark:text-amber-300 dark:ring-amber-500/20',
            'dot' => 'before:bg-amber-500',
            'switch' => 'bg-amber-400',
            'pill' => 'bg-amber-300 border-amber-400',
            'card' => 'border-l-4 border-l-amber-500 bg-amber-50 dark:bg-amber-900/20',
        ],
    ];

    $status = $statusMap[$statut] ?? $statusMap['default'];
@endphp

@switch($style)
    @case('badge')
        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $status['badge'] }}">
            {{ $status['text'] }}
        </span>
        @break

    @case('dot')
        <span class="inline-flex items-center before:mr-1.5 before:h-2 before:w-2 before:rounded-full before:content-[''] {{ $status['dot'] }}">
            {{ $status['text'] }}
        </span>
        @break

    @case('switch')
        <span class="inline-flex items-center gap-2">
            <span class="relative inline-flex h-5 w-10 flex-shrink-0 rounded-full {{ $status['switch'] }} transition-colors duration-200">
                @if($statut == 1)
                    <span class="translate-x-5 inline-block h-4 w-4 transform rounded-full bg-white shadow-md transition duration-200 ease-in-out mt-0.5 ml-0.5"></span>
                @else
                    <span class="translate-x-0.5 inline-block h-4 w-4 transform rounded-full bg-white shadow-md transition duration-200 ease-in-out mt-0.5"></span>
                @endif
            </span>
            {{ $status['text'] }}
        </span>
        @break

    @case('pill')
        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium border {{ $status['pill'] }} text-white">
            {{ $status['text'] }}
        </span>
        @break

    @case('card')
        <div class="p-2 {{ $status['card'] }} rounded">
            <span class="font-medium">{{ $status['text'] }}</span>
        </div>
        @break

    @default
        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $status['badge'] }}">
            {{ $status['text'] }}
        </span>
@endswitch
