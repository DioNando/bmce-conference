@props(['content' => '', 'class' => '', 'align' => 'text-left'])

<td class="py-5 px-5 text-sm whitespace-nowrap {{ $align }} {{ $class }}"
    {{ $attributes->merge(['class' => $class]) }}>
    @if ($content)
        {!! $content !!}
    @elseif(!$slot->isEmpty())
        {{ $slot }}
    @else
        <span class="text-xs text-gray-600 dark:text-gray-400">N/A</span>
    @endif
</td>
