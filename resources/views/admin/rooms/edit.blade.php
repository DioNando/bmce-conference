<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.rooms.index') }}" class="text-primary">{{ __('Rooms') }}</a></li>
            <li>{{ __('Edit') }}</li>
        </ul>
    </div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-primary">{{ __('Edit Room') }}</h2>
    </div>
    <div class="card card-bordered bg-base-100 shadow-lg">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
                @csrf
                @method('PUT')

                <!-- Room Name -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('Room Name') }}</legend>
                    <input id="name" type="text" name="name" value="{{ old('name', $room->name) }}"
                        class="input input-bordered w-full" required autofocus />
                    @error('name')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Capacity -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Capacity (persons)') }}</legend>
                    <input id="capacity" type="number" min="1" name="capacity" value="{{ old('capacity', $room->capacity) }}"
                        class="input input-bordered w-full" required />
                    @error('capacity')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Location -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Location') }}</legend>
                    <input id="location" type="text" name="location" value="{{ old('location', $room->location) }}"
                        class="input input-bordered w-full" required />
                    @error('location')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-ghost">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Update Room') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
