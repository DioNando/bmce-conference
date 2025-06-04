<x-guest-layout>
    <div class="card card-border card-md bg-base-100 w-full max-w-md mx-auto shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center">{{ __('Verify Email') }}</h2>

            <div class="text-sm text-base-content mb-4">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</span>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-4 mt-4">
                <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('Resend Verification Email') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-neutral w-full">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
