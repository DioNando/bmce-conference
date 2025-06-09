<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-primary">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <section class="space-y-6 py-6">
        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-primary">{{ __('Toutes les notifications') }}</h3>
                    <div class="flex space-x-2">
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline">
                                {{ __('Tout marquer comme lu') }}
                            </button>
                        </form>
                        <form action="{{ route('notifications.delete-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-error btn-outline"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer toutes les notifications lues ?')">
                                {{ __('Supprimer les notifications lues') }}
                            </button>
                        </form>
                    </div>
                </div>

                @if (session('success'))
                    <div role="alert" class="alert alert-success mb-4">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($notifications->isEmpty())
                    <div class="p-10 text-center text-base-content/70">
                        <x-heroicon-o-bell-slash class="size-16 mx-auto text-base-content/50 mb-4" />
                        <p class="text-lg">{{ __('Aucune notification') }}</p>
                        <p class="mt-2">{{ __('Vous n\'avez pas encore reçu de notifications.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ __('Notification') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notifications as $notification)
                                    <tr class="{{ $notification->read_at ? 'opacity-70' : 'font-medium' }}">
                                        <td>
                                            <div class="avatar {{ $notification->read_at ? '' : 'online' }}">
                                                <div class="w-10 rounded-full">
                                                    @php
                                                        $avatarBg = match($notification->type) {
                                                            'meeting' => '2563eb',
                                                            'question' => 'f59e0b',
                                                            'system' => '10b981',
                                                            default => 'random'
                                                        };
                                                        $avatarName = match($notification->type) {
                                                            'meeting' => 'Meeting',
                                                            'question' => 'Question',
                                                            'system' => 'System',
                                                            default => 'Notification'
                                                        };
                                                    @endphp
                                                    <img src="https://ui-avatars.com/api/?name={{ $avatarName }}&background={{ $avatarBg }}&color=fff" alt="Avatar" />
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="{{ $notification->read_at ? 'text-base-content' : 'text-primary' }}">
                                                    {{ $notification->title }}
                                                </div>
                                                <div class="text-sm opacity-80">{{ $notification->message }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ $notification->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs opacity-70">{{ $notification->created_at->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            <div class="flex space-x-2">
                                                @if ($notification->related_id && $notification->related_type === 'App\\Models\\Meeting')
                                                    @php
                                                        $route = Auth::user()->isAdmin() ? 'admin.meetings.show' :
                                                               (Auth::user()->isIssuer() ? 'issuer.meetings.show' : 'investor.meetings.show');
                                                    @endphp
                                                    <a href="{{ route($route, $notification->related_id) }}"
                                                        class="btn btn-xs btn-primary">
                                                        {{ __('Voir détails') }}
                                                    </a>
                                                @endif

                                                <form action="{{ $notification->read_at
                                                    ? route('notifications.mark-as-unread', $notification->id)
                                                    : route('notifications.mark-as-read', $notification->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-ghost">
                                                        {{ $notification->read_at
                                                            ? __('Marquer comme non-lu')
                                                            : __('Marquer comme lu') }}
                                                    </button>
                                                </form>

                                                <form action="{{ route('notifications.delete', $notification->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-ghost text-error"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')">
                                                        {{ __('Supprimer') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
