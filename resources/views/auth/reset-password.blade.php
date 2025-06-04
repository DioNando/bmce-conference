<x-guest-layout>
    <div class="card card-border card-md bg-base-100 w-full max-w-md mx-auto shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center">{{ __('Reset Password') }}</h2>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('Email') }}</legend>
                    <input type="email" name="email" id="email" :value="old('email', $request->email)"
                           class="input input-primary w-full" required autofocus autocomplete="username" />
                    @error('email')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Password -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Password') }}</legend>
                    <input type="password" name="password" id="password"
                           class="input input-primary w-full" required autocomplete="new-password" />
                    @error('password')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Confirm Password -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Confirm Password') }}</legend>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="input input-primary w-full" required autocomplete="new-password" />
                    @error('password_confirmation')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="card-actions mt-6">
                    <button type="submit" class="btn btn-primary ml-auto">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
