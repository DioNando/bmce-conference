@props(['label' => '', 'items' => [], 'color' => 'blue', 'emptyText' => 'Aucun élément disponible'])

<div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
    <dt class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</dt>
    <dd class="mt-1 {{ count($items) > 0 ? 'text-sm text-gray-700 dark:text-gray-300' : 'text-xs text-gray-600 dark:text-gray-400' }} sm:col-span-2 sm:mt-0">
        @if (count($items) > 0)
            <x-badge.list :items="$items" :color="$color" :emptyText="$emptyText" />
        @else
            N/A
        @endif
    </dd>
</div>
