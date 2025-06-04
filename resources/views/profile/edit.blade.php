<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between">
            <div>
                <div class="breadcrumbs text-sm">
                    <ul>
                        <li><a href="{{ route('dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                        <li>{{ __('Profile') }}</li>
                    </ul>
                </div>
                <h1 class="mt-4 text-2xl font-bold text-primary">
                    {{ __('Profile Settings') }}
                </h1>
            </div>
        </div>
    </x-slot>

    <section class="space-y-6 max-w-3xl">
        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body">
                <section>
                    <header>
                        <h2 class="text-xl font-medium text-warning">{{ __('Account Management') }}</h2>
                    </header>
                    <form method="POST" action="{{ route('logout') }}" class="mt-6">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <span>{{ __('Log Out') }}</span>
                        </button>
                    </form>
                </section>
            </div>
        </div>

        @if (auth()->user()->hasRole(\App\Enums\UserRole::ADMIN->value))
            <div class="card card-bordered bg-danger shadow-lg">
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        @endif
    </section>
</x-app-layout>
