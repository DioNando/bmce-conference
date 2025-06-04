@props([
    'content' => '',
    'align' => 'text-left',
    'sortable' => false,
    'sortField' => null,
    'currentSortBy' => null,
    'currentSortOrder' => 'asc',
])

<th scope="col" class="w-fit uppercase px-5 py-3.5 {{ $align }} text-xs font-semibold">
    @if ($sortable && $sortField)
        <a href="{{ request()->fullUrlWithQuery([
            'sort_by' => $sortField,
            'sort_order' => $currentSortBy === $sortField && $currentSortOrder === 'asc' ? 'desc' : 'asc',
        ]) }}"
            class="flex items-center gap-1 hover:text-blue-600">
            {{ $content }}

            @if ($currentSortBy !== $sortField)
                <span class="inline-flex flex-col">
                    <x-heroicon-s-chevron-up-down class="size-4" />
                </span>
            @elseif($currentSortOrder === 'asc')
                <x-heroicon-s-chevron-up class="size-3" />
            @else
                <x-heroicon-s-chevron-down class="size-3" />
            @endif
        </a>
    @else
        {{ $content }}
    @endif
</th>
