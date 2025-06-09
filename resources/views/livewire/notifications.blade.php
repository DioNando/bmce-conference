@props(['count' => 0, 'notifications' => []])

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
            <div class="px-4 py-3 bg-primary text-primary-content flex justify-between items-center">
                <h3 class="text-sm font-semibold">Notifications</h3>
                @if ($count > 0)
                    <button wire:click="markAllAsRead" class="btn btn-ghost btn-xs text-primary-content">Tout marquer comme lu</button>
                @endif
            </div>

            {{-- Notification List --}}
            <div class="max-h-96 overflow-y-auto">
                @forelse ($notifications as $notification)
                    <div class="list-row px-4 py-3 hover:bg-base-200 border-b border-base-200 {{ $notification['read'] ? '' : 'bg-base-200' }}">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <div class="avatar {{ $notification['read'] ? '' : 'online' }}">
                                    <div class="w-10 rounded-full">
                                        <img src="{{ $notification['avatar'] ?? 'https://ui-avatars.com/api/?name=Meeting&background=random' }}" alt="Avatar" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <p class="text-sm font-medium {{ $notification['read'] ? 'text-base-content' : 'text-primary' }}">
                                        {{ $notification['title'] }}
                                    </p>
                                    <span class="text-xs text-base-content/70">{{ $notification['time'] }}</span>
                                </div>
                                <p class="text-xs text-base-content/70 mt-1">{{ $notification['message'] }}</p>
                                <div class="mt-2">
                                    <a href="{{ $notification['action_url'] }}" class="btn btn-xs btn-primary">
                                        {{ $notification['action_text'] ?? 'Voir détails' }}
                                    </a>
                                    <button wire:click="markAsRead('{{ $notification['id'] }}')" class="btn btn-xs btn-ghost">
                                        {{ $notification['read'] ? 'Marquer comme non-lu' : 'Marquer comme lu' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-base-content/70">
                        <div class="py-8">
                            <x-heroicon-o-bell-slash class="size-12 mx-auto text-base-content/50" />
                            <p class="mt-2">Aucune notification</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if (count($notifications) > 0)
                <div class="p-3 border-t border-base-200 bg-base-100">
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-block">
                        Voir toutes les notifications
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
