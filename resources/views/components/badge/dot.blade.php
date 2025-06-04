@props(['color' => 'gray', 'size' => 'md', 'rounded' => 'full', 'label' => ''])

@php
    $colors = [
        'blue' => 'bg-blue-500 text-blue-800 dark:bg-blue-600 dark:text-blue-300',
        'green' => 'bg-green-500 text-green-800 dark:bg-green-600 dark:text-green-300',
        'purple' => 'bg-purple-500 text-purple-800 dark:bg-purple-600 dark:text-purple-300',
        'yellow' => 'bg-yellow-500 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-300',
        'orange' => 'bg-orange-500 text-orange-800 dark:bg-orange-600 dark:text-orange-300',
        'red' => 'bg-red-500 text-red-800 dark:bg-red-600 dark:text-red-300',
        'indigo' => 'bg-indigo-500 text-indigo-800 dark:bg-indigo-600 dark:text-indigo-300',
        'pink' => 'bg-pink-500 text-pink-800 dark:bg-pink-600 dark:text-pink-300',
        'rose' => 'bg-rose-500 text-rose-800 dark:bg-rose-600 dark:text-rose-300',
        'gray' => 'bg-gray-500 text-gray-800 dark:bg-gray-600 dark:text-gray-300',
        'cyan' => 'bg-cyan-500 text-cyan-800 dark:bg-cyan-600 dark:text-cyan-300',
        'emerald' => 'bg-emerald-500 text-emerald-800 dark:bg-emerald-600 dark:text-emerald-300',
        'sky' => 'bg-sky-500 text-sky-800 dark:bg-sky-600 dark:text-sky-300',
        'amber' => 'bg-amber-500 text-amber-800 dark:bg-amber-600 dark:text-amber-300',
        'lime' => 'bg-lime-500 text-lime-800 dark:bg-lime-600 dark:text-lime-300',
        'violet' => 'bg-violet-500 text-violet-800 dark:bg-violet-600 dark:text-violet-300',
        'fuchsia' => 'bg-fuchsia-500 text-fuchsia-800 dark:bg-fuchsia-600 dark:text-fuchsia-300',
        'slate' => 'bg-slate-500 text-slate-800 dark:bg-slate-600 dark:text-slate-300',
        'zinc' => 'bg-zinc-500 text-zinc-800 dark:bg-zinc-600 dark:text-zinc-300',
        'neutral' => 'bg-neutral-500 text-neutral-800 dark:bg-neutral-600 dark:text-neutral-300',
    ];

    $sizes = [
        'xs' => 'px-2 py-0.5 text-xs',
        'sm' => 'px-2.5 py-0.5 text-sm',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
        'xl' => 'px-5 py-2 text-lg',
    ];

    $roundedVariants = [
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
    ];

    $colorClasses = $colors[$color] ?? $colors['gray'];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];

    $roundedParam = $rounded;
    $roundedClasses = $roundedVariants[$roundedParam] ?? $roundedVariants['full'];

    $dimensionClasses = $label ? $sizeClasses : 'h-2 w-2';
@endphp

<span
    {{ $attributes->merge(['class' => "inline-flex items-center font-medium $dimensionClasses $colorClasses $roundedClasses"]) }}>
    @if ($label)
        <span class="text-xs">{{ $label }}</span>
    @endif
</span>
