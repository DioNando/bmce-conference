<section>
    <header>
        <h2 class="text-xl font-medium text-primary">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-base-content/70">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('Last Name') }}</legend>
            <input id="name" name="name" type="text" class="input input-bordered w-full" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')
                <p class="label text-error text-sm">{{ $message }}</p>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('First Name') }}</legend>
            <input id="first_name" name="first_name" type="text" class="input input-bordered w-full" value="{{ old('first_name', $user->first_name) }}" required autocomplete="given-name" />
            @error('first_name')
                <p class="label text-error text-sm">{{ $message }}</p>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('Email') }}</legend>
            @if(auth()->user()->hasRole(\App\Enums\UserRole::ADMIN->value))
                <input id="email" name="email" type="email" class="input input-bordered w-full" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @else
                <input id="email" name="email" type="email" class="input input-bordered w-full input-disabled" value="{{ old('email', $user->email) }}" autocomplete="username" readonly />
                <p class="label text-base-content/70 text-sm">{{ __('Please contact the administrator to change your email address.') }}</p>
            @endif
            @error('email')
                <p class="label text-error text-sm">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <div class="alert alert-warning">
                        <span>{{ __('Your email address is unverified.') }}</span>
                        <button form="send-verification" class="btn btn-ghost btn-sm">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2">
                            <span>{{ __('A new verification link has been sent to your email address.') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </fieldset>

        <div class="flex items-center gap-4 mt-6">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
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
