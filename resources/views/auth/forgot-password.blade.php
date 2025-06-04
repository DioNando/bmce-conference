<x-guest-layout>
    <div class="card card-border card-md bg-base-100 w-full max-w-md mx-auto shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center">{{ __('Forgot Password') }}</h2>

            <div class="text-sm text-base-content mb-4">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('Email') }}</legend>
                    <input type="email" name="email" id="email" :value="old('email')"
                           class="input input-primary w-full" required autofocus />
                    @error('email')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div class="card-actions mt-6">
                    <div class="flex items-center justify-end w-full">
                        <a href="{{ route('login') }}" class="link link-primary text-sm">
                            {{ __('Back to login') }}
                        </a>

                        <button type="submit" class="btn btn-primary ml-auto">
                            {{ __('Email Password Reset Link') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
