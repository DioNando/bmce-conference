<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.administrators') }}" class="text-primary">{{ __('Administrators') }}</a></li>
            <li>{{ __('Create') }}</li>
        </ul>
    </div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-primary">Add New Administrator</h2>
    </div>
    <div class="card card-bordered bg-base-100 shadow-lg">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.administrators.store') }}">
                @csrf

                <!-- Name -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('Name') }}</legend>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        class="input input-bordered w-full" required autofocus />
                    @error('name')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- First Name -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('First Name') }}</legend>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                        class="input input-bordered w-full" required />
                    @error('first_name')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Email Address -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Email') }}</legend>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="input input-bordered w-full" required />
                    @error('email')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Password -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Password') }}</legend>
                    <input id="password" type="password" name="password"
                        class="input input-bordered w-full" required />
                    @error('password')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Confirm Password -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Confirm Password') }}</legend>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="input input-bordered w-full" required />
                    @error('password_confirmation')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('admin.administrators') }}"
                        class="btn btn-ghost">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Create Administrator') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
