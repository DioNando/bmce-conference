<section>
    <header>
        <h2 class="text-xl font-medium text-primary">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-base-content/70">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('Current Password') }}</legend>
            <input id="update_password_current_password" name="current_password" type="password" class="input input-bordered w-full" autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <p class="label text-error text-sm">{{ $message }}</p>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('New Password') }}</legend>
            <input id="update_password_password" name="password" type="password" class="input input-bordered w-full" autocomplete="new-password" />
            @error('password', 'updatePassword')
                <p class="label text-error text-sm">{{ $message }}</p>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('Confirm Password') }}</legend>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="input input-bordered w-full" autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <p class="label text-error text-sm">{{ $message }}</p>
            @enderror
        </fieldset>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-success"
                >{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
