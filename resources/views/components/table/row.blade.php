@props(['content' => '', 'class' => ''])

<tr {{ $attributes->merge(['class' => $class]) }}>
    {{ $content }}
    {{ $slot }}
</tr>
