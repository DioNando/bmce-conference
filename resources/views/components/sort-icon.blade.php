@props(['field' => '', 'currentField' => '', 'currentOrder' => 'asc'])

@php
$isActive = $currentField === $field;
$sortDirection = $isActive && $currentOrder === 'asc' ? 'desc' : 'asc';
@endphp

<span class="ml-1">
    @if (!$isActive)
        {{-- Default (unsorted) state - shows both up and down arrows --}}
        <x-heroicon-s-arrows-up-down class="size-3.5 opacity-50" />
    @elseif ($currentOrder === 'asc')
        {{-- Ascending order - arrow pointing up --}}
        <x-heroicon-s-arrow-up class="size-3.5 text-primary" />
    @else
        {{-- Descending order - arrow pointing down --}}
        <x-heroicon-s-arrow-down class="size-3.5 text-primary" />
    @endif
</span>
