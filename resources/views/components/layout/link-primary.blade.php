@props(['route', 'icon', 'label'])

<a href="{{ route($route) }}"
    class="sidebar-link group flex items-center gap-x-2 rounded-lg px-2 py-1.5 text-sm/6 font-semibold
        {{ request()->routeIs($route)
           ? 'bg-white text-primary mobile-active'
           : 'text-white hover:bg-white/10 hover:text-white mobile-inactive' }}">
    <x-dynamic-component :component="'heroicon-s-' . $icon"
        class="sidebar-icon shrink-0" />
    <span class="sidebar-label text-nowrap">{{ __($label) }}</span>
</a>
