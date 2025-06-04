<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="card card-border card-md bg-base-100 w-full min-w-md mx-auto shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center">{{ __('Login') }}</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('Email') }}</legend>
                    <input type="email" name="email" id="email" :value="old('email')"
                           class="input input-primary w-full" required autofocus autocomplete="username" />
                    @error('email')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Password -->
                <fieldset class="fieldset mt-4">
                    <legend class="fieldset-legend">{{ __('Password') }}</legend>
                    <input type="password" name="password" id="password"
                           class="input input-primary w-full" required autocomplete="current-password" />
                    @error('password')
                        <p class="label text-error text-sm">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- Remember Me -->
                <div class="form-control mt-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" name="remember" id="remember_me" />
                        <span class="label-text">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="card-actions mt-6">
                    <div class="flex items-center justify-between w-full">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="link link-primary text-sm">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary">
                            {{ __('Log in') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
