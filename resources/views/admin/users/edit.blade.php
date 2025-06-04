<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="text-primary">{{ __('Accounts') }}</a></li>
                <li><a href="{{ route('admin.users.show', $user) }}" class="text-primary">{{ $user->name }}</a></li>
            <li>{{ __('Edit') }}</li>
        </ul>
    </div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-primary">{{ __('Edit Account') }}</h2>
    </div>
    <div class="card card-bordered bg-base-100 shadow-lg">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                {{-- Personal Information Section --}}
                <section class="mb-6 border-b border-base-200 pb-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h2 class="text-xl font-medium text-primary mb-4">
                            {{ __('Personal Information') }}
                        </h2>
                        <p>
                            {{ __('Update the personal information for this account.') }}
                        </p>
                    </div>

                    <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8">
                        <!-- First Name -->
                        <fieldset class="fieldset mb-4 sm:col-span-1">
                            <legend class="fieldset-legend">{{ __('First Name') }}</legend>
                            <input id="first_name" type="text" name="first_name"
                                value="{{ old('first_name', $user->first_name) }}" class="input input-bordered w-full" required
                                autofocus />
                            @error('first_name')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Last Name -->
                        <fieldset class="fieldset mb-4 sm:col-span-1">
                            <legend class="fieldset-legend">{{ __('Last Name') }}</legend>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="input input-bordered w-full" required />
                            @error('name')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Email -->
                        <fieldset class="fieldset mb-4 sm:col-span-full">
                            <legend class="fieldset-legend">{{ __('Email') }}</legend>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="input input-bordered w-full" required />
                            @error('email')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Phone -->
                        <fieldset class="fieldset mb-4 sm:col-span-full">
                            <legend class="fieldset-legend">{{ __('Phone') }}</legend>
                            <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="input input-bordered w-full" />
                            @error('phone')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>
                    </div>
                </section>

                {{-- Account Details Section --}}
                <section class="mb-6 border-b border-base-200 pb-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h2 class="text-xl font-medium text-primary mb-4">
                            {{ __('Account Details') }}
                        </h2>
                        <p>
                            {{ __('Modify credentials and professional information.') }}
                        </p>
                    </div>

                    <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8">
                        <!-- Position -->
                        <fieldset class="fieldset mb-4 sm:col-span-2">
                            <legend class="fieldset-legend">{{ __('Position') }}</legend>
                            <input id="position" type="text" name="position" value="{{ old('position', $user->position) }}"
                                class="input input-bordered w-full" />
                            @error('position')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Password -->
                        <fieldset class="fieldset mb-4 sm:col-span-1">
                            <legend class="fieldset-legend">{{ __('Password') }}</legend>
                            <input id="password" type="password" name="password" class="input input-bordered w-full" />
                            <p class="label text-base-content/70 text-sm">{{ __('Leave empty to keep the current password') }}
                            </p>
                            @error('password')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Confirm Password -->
                        <fieldset class="fieldset mb-4 sm:col-span-1">
                            <legend class="fieldset-legend">{{ __('Confirm Password') }}</legend>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="input input-bordered w-full" />
                            @error('password_confirmation')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>
                    </div>
                </section>

                {{-- Organization & Role Section --}}
                <section class="mb-6 border-b border-base-200 pb-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h2 class="text-xl font-medium text-primary mb-4">
                            {{ __('Organization & Permissions') }}
                        </h2>
                        <p>
                            {{ __('Modify organization and role permissions for this account.') }}
                        </p>
                    </div>

                    <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8">
                        <!-- Organization -->
                        <fieldset class="fieldset mb-4 sm:col-span-1">
                            <legend class="fieldset-legend">{{ __('Organization') }}</legend>
                            <select id="organization_id" name="organization_id" class="select select-bordered w-full" required>
                                <option value="" disabled>{{ __('-- Select --') }}</option>
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}"
                                        {{ old('organization_id', $user->organization_id) == $organization->id ? 'selected' : '' }}>
                                        {{ $organization->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organization_id')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <!-- Role -->
                        <fieldset class="fieldset mb-4 sm:col-span-1">
                            <legend class="fieldset-legend">{{ __('Role') }}</legend>
                            <select id="role" name="role" class="select select-bordered w-full" required>
                                <option value="" disabled>{{ __('-- Select --') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="label text-error text-sm">{{ $message }}</p>
                            @enderror
                        </fieldset>
                    </div>
                </section>

                {{-- Account Settings Section --}}
                <section class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h2 class="text-xl font-medium text-primary mb-4">
                            {{ __('Account Settings') }}
                        </h2>
                        <p>
                            {{ __('Configure the status of this account.') }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        <!-- Status -->
                        <fieldset class="fieldset mb-4">
                            <label class="fieldset-legend cursor-pointer justify-start gap-2">
                                <input id="status" name="status" type="checkbox" value="1"
                                    {{ old('status', $user->status) ? 'checked' : '' }}
                                    class="checkbox checkbox-sm checkbox-primary" />
                                <span>{{ __('Active') }}</span>
                            </label>
                        </fieldset>
                    </div>
                </section>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Update Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
