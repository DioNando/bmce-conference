<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li>{{ __('Rooms') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Room Management') }}
            </h3>
            <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                <x-heroicon-s-plus class="size-5" />
                <span class="hidden lg:block">{{ __('New Room') }}</span>
            </a>
        </div>
    </x-slot>
    <section class="space-y-6">


        @if (session('success'))
            <div role="alert" class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="alert alert-error">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="card card-bordered bg-base-200 shadow-lg">
            <div class="card-body">
                <div class="card bg-base-100 shadow-md overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Room Name') }}</th>
                                <th>{{ __('Capacity') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th class="text-right">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                                <tr>
                                    <td class="font-medium">{{ $room->name }}</td>
                                    <td>{{ $room->capacity }} persons</td>
                                    <td>{{ $room->location }}</td>
                                    <td class="text-right">
                                        <div class="flex gap-2 items-center justify-end">
                                            <a href="{{ route('admin.rooms.edit', $room) }}"
                                                class="btn btn-sm btn-success">{{ __('Edit') }}</a>
                                            <button type="button"
                                                @click="$dispatch('open-modal', 'delete-room-{{ $room->id }}')"
                                                class="btn btn-sm btn-square btn-error"><x-heroicon-s-trash
                                                    class="size-4" /></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-base-content/70">
                                        {{ __('No rooms found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $rooms->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </section>

    <!-- Deletion Confirmation Modals -->
    @foreach ($rooms as $room)
        <x-modal name="delete-room-{{ $room->id }}" :show="false" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium text-error">
                    {{ __('Confirm Room Deletion') }}
                </h2>

                <p class="mt-3 text-sm text-base-content/70">
                    {{ __('Are you sure you want to delete') }} <strong>{{ $room->name }}</strong>?
                    {{ __('This action cannot be undone.') }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </button>

                    <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">
                            {{ __('Delete Room') }}
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    @endforeach
</x-app-layout>
