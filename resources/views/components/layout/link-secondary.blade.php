@props(['route' => '', 'label' => '', 'icon' => ''])
<a href="{{ route($route) }}"
    class="group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold
    text-gray-700 hover:bg-gray-50 hover:text-blue-600
    dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-blue-300">

    <span
        class="flex size-6 shrink-0 items-center justify-center
        rounded-lg border border-gray-200 bg-white text-[0.625rem] font-medium
        text-gray-400 group-hover:border-blue-600 group-hover:text-blue-600
        dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500
        dark:group-hover:border-blue-300 dark:group-hover:text-blue-300">
        {{ $icon }}
    </span>
    <span class="truncate">{{ $label }}</span>
</a>
