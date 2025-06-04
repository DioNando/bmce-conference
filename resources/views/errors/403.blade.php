<x-error-layout>
    <div class="flex flex-col items-center justify-center min-h-screen p-4 text-center">
        <div class="card max-w-md bg-base-100 shadow-xl">
            <div class="card-body">
                <h1 class="text-9xl font-bold text-error">403</h1>
                <div class="divider divider-error"></div>
                <h2 class="card-title text-2xl mb-2">{{ __('Forbidden') }}</h2>
                <p class="mb-6">{{ __('You do not have permission to access this resource.') }}</p>
                <div class="card-actions justify-center">
                    <a href="{{ url()->previous() }}" class="btn btn-outline">
                        {{ __('Go Back') }}
                    </a>
                    @if(isset($isAuthenticated) && $isAuthenticated)
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            {{ __('Return to Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            {{ __('Login') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-error-layout>
