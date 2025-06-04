<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('issuer.dashboard') }}" class="text-primary">{{ __('Issuer Dashboard') }}</a></li>
            <li class="text-base-content/70">{{ __('My QR Code') }}</li>
        </ul>
    </div>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-base-content">{{ __('My QR Code') }}</h1>
                <p class="text-base-content/70 mt-1">{{ __('Download and share your personal QR code') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('issuer.qr-code.download') }}" class="btn btn-primary">
                   <x-heroicon-s-document-text class="size-4" />
                <span class="hidden lg:block"> {{ __('Download QR Code PDF') }}
                </span>
                </a>
            </div>
        </div>

        <!-- QR Code Display Card -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- QR Code -->
            <div class="card card-border bg-base-100 shadow-lg">
                <div class="card-body items-center text-center">
                    <h2 class="card-title text-lg mb-4">{{ __('Your QR Code') }}</h2>                    <div class="bg-white p-6 rounded-lg shadow-inner">
                        <img src="{{ $qrCodeUrl }}" alt="{{ __('QR Code') }}" class="mx-auto">
                    </div>
                    <div class="mt-4 text-sm text-base-content/70">
                        <p>{{ __('QR Code URL') }}:</p>
                        <code class="text-xs bg-base-200 px-2 py-1 rounded break-all">{{ $qrContent }}</code>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="card card-border bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-lg mb-4">{{ __('Your Information') }}</h2>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if ($user->profile_photo_path)
                                    <img class="h-16 w-16 rounded-full object-cover" src="{{ $user->profile_photo_url }}"
                                        alt="{{ $user->name }}">
                                @else
                                    <div
                                        class="h-16 w-16 rounded-full bg-primary flex items-center justify-center text-white text-xl font-semibold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-base-content">
                                    {{ $user->name . ' ' . $user->first_name }}
                                </h4>
                                <p class="text-base-content/80">{{ $user->email }}</p>
                                @if($user->organization)
                                    <p class="text-sm text-base-content/60">{{ $user->organization->name }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div class="grid grid-cols-1 gap-3">
                            <div class="flex justify-between">
                                <span class="text-base-content/70">{{ __('User ID') }}:</span>
                                <span class="font-medium">{{ $user->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">{{ __('Role') }}:</span>
                                <span class="badge badge-secondary">{{ __('Issuer') }}</span>
                            </div>
                            @if($user->phone)
                                <div class="flex justify-between">
                                    <span class="text-base-content/70">{{ __('Phone') }}:</span>
                                    <span class="font-medium">{{ $user->phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions Card -->
        <div class="card card-border bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">{{ __('How to use your QR Code') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">{{ __('Download') }}</h3>
                        <p class="text-sm text-base-content/70">{{ __('Download your QR code as a PDF document for easy sharing') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">{{ __('Share') }}</h3>
                        <p class="text-sm text-base-content/70">{{ __('Share your QR code with meeting participants and stakeholders') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold mb-2">{{ __('Quick Access') }}</h3>
                        <p class="text-sm text-base-content/70">{{ __('Scanning your QR code provides instant access to your issuer profile') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
