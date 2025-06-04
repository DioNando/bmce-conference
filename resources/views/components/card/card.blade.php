@props(['defaultOpen' => true])

<div x-data="{ open: {{ $defaultOpen }} }" x-cloak
    {{ $attributes->merge(['class' => 'rounded-lg border overflow-hidden ' . $typeClass() . ' ' . $class]) }}>
    {{ $slot }}
</div>
