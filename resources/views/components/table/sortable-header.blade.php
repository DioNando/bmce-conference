@props(['label', 'field' => null, 'sortable' => false, 'align' => 'left'])

@php
    $currentSortField = request('sort_by', '');
    $currentSortOrder = request('sort_order', 'asc');
    $newOrder = ($currentSortField === $field && $currentSortOrder === 'asc') ? 'desc' : 'asc';
    $sortUrl = request()->fullUrlWithQuery([
        'sort_by' => $field,
        'sort_order' => $newOrder,
    ]);
    $alignClass = match($align) {
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left'
    };
    $isActive = $currentSortField === $field;
@endphp

<th class="{{ $alignClass }}">
    @if ($sortable && $field)
        <a href="{{ $sortUrl }}" class="flex items-center {{ $align === 'center' ? 'justify-center' : ($align === 'right' ? 'justify-end' : '') }} hover:text-primary transition-colors {{ $isActive ? 'text-primary font-medium' : '' }}">
            {{ __($label) }}
            <x-sort-icon field="{{ $field }}" currentField="{{ $currentSortField }}" currentOrder="{{ $currentSortOrder }}" />
        </a>
    @else
        {{ __($label) }}
    @endif
</th>
