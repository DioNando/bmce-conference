<x-guest-layout>
    <div class="card card-border card-md bg-base-100 w-full max-w-md mx-auto shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center">{{ __('Register') }}</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('Name') }}</legend>
                    <input type="text" name="name" id="name" :value="old('name')"
                           class="input input-primary w-full" required autofocus autocomplete="name" />
                    @error('name')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Email Address -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Email') }}</legend>
                    <input type="email" name="email" id="email" :value="old('email')"
                           class="input input-primary w-full" required autocomplete="username" />
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
                    <div class="flex items-center justify-between w-full">
                        <a href="{{ route('login') }}" class="link link-primary text-sm">
                            {{ __('Already registered?') }}
                        </a>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Register') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
