<x-guest-layout>
    <div class="card card-border card-md bg-base-100 w-full max-w-md mx-auto shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center">{{ __('Confirm Password') }}</h2>

            <div class="text-sm text-base-content mb-4">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <!-- Password -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('Password') }}</legend>
                    <input type="password" name="password" id="password"
                           class="input input-primary w-full" required autocomplete="current-password" />
                    @error('password')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="card-actions mt-6">
                    <button type="submit" class="btn btn-primary ml-auto">
                        {{ __('Confirm') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
