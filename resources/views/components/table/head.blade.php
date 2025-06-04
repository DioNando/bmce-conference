@props(['color' => 'blue', 'background' => true])

@php
    $colorClass = function () use ($color) {
        return match ($color) {
            'red' => 'text-red-900 dark:text-red-300',
            'blue' => 'text-blue-900 dark:text-blue-300',
            'green' => 'text-green-900 dark:text-green-300',
            'orange' => 'text-orange-900 dark:text-orange-300',
            'yellow' => 'text-yellow-900 dark:text-yellow-300',
            'purple' => 'text-purple-900 dark:text-purple-300',
            'indigo' => 'text-indigo-900 dark:text-indigo-300',
            'gray' => 'text-gray-900 dark:text-gray-300',
            default => '',
        };
    };

    $bgClass = function () use ($color) {
        return match ($color) {
            'red' => 'bg-red-100 dark:bg-red-900/70',
            'blue' => 'bg-blue-100 dark:bg-blue-900/70',
            'green' => 'bg-green-100 dark:bg-green-900/70',
            'orange' => 'bg-orange-100 dark:bg-orange-900/70',
            'yellow' => 'bg-yellow-100 dark:bg-yellow-900/70',
            'purple' => 'bg-purple-100 dark:bg-purple-900/70',
            'indigo' => 'bg-indigo-100 dark:bg-indigo-900/70',
            'gray' => 'bg-gray-100 dark:bg-gray-900/70',
            default => '',
        };
    };
@endphp

<thead class="{{ $colorClass() }} {{ $background ? $bgClass() : '' }} border-b border-gray-200 dark:border-gray-700">
    <tr>
        {{-- @foreach ($headers as $header)
            <th scope="col"
                class="uppercase px-5 py-3.5 {{ $align }} text-xs font-semibold">
                {{ $header }}
            </th>
        @endforeach --}}
        {{ $slot }}
    </tr>
</thead>
