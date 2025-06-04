@props(['count' => 0])

<div x-data="{ open: false }" class="relative flex items-center mr-3">
    <button @click="open = !open" @click.away="open = false" type="button"
        class="relative btn btn-ghost btn-circle text-primary">
        <span class="sr-only">Voir les notifications</span>
        <x-heroicon-s-bell class="size-6" />
        @if ($count > 0)
            <span
                class="absolute -top-1 -right-1 flex size-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-medium text-white">
                {{ $count }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95" @click.away="open = false"
        class="fixed sm:absolute right-0 top-16 sm:top-8 p-3 sm:px-0 z-50 w-full sm:w-96">
        <div class="card shadow-lg bg-base-100 overflow-hidden rounded-box">
            <div class="px-4 pt-3 pb-4 bg-primary text-white">
                <h3 class="text-sm font-semibold">Notifications</h3>
            </div>
            {{-- Notification List --}}
            <div class="px-4 pt-3 pb-4">
                <a href="#"
                    class="text-sm font-medium text-primary link">
                    Voir toutes les notifications
                </a>
            </div>
        </div>
    </div>
</div>
