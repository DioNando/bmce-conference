@props(['label' => '', 'value' => ''])

<div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
    <dt class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</dt>
    <dd class="mt-1 {{ $value ? 'text-sm text-gray-700 dark:text-gray-300' : 'text-xs text-gray-600 dark:text-gray-400' }} sm:col-span-2 sm:mt-0">{{ $value !== false ? ($value ?: 'N/A') : 'Non' }}</dd>
</div>
