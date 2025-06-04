@props(['divider' => null, 'class' => 'p-6'])

<div x-show="open" x-cloak
    {{ $attributes->merge(['class' => $class . ' ' . ($divider ? 'divide-y divide-gray-200 dark:divide-gray-700' : '')]) }}>
    {{ $slot }}
</div>
