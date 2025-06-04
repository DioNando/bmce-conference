<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.meetings.index') }}" class="text-primary">{{ __('Meetings') }}</a></li>
            <li><a href="{{ route('admin.meetings.show', $meeting) }}"
                    class="text-primary">{{ __('Meeting Details') }}</a></li>
            <li>{{ __('QR Code') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-primary">{{ __('Attendance QR Code') }}</h1>
            <a href="{{ route('admin.meetings.qrcode.export-pdf', ['meeting' => $meeting->id, 'investor' => $investor->id]) }}"
                class="btn btn-primary">
                <x-heroicon-s-document-text class="size-4" />
                <span class="hidden lg:block"> {{ __('Download QR Code PDF') }}
                </span>
            </a>
        </div>

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body items-center text-center">
                <h2 class="card-title text-primary">{{ __('Meeting Attendance QR Code') }}</h2>

                <div class="divider"></div>

                <!-- Investor Information -->
                <div class="mb-4">
                    <h3 class="font-bold text-lg">{{ $investor->investor->name }}
                        {{ $investor->investor->first_name }}</h3>
                    @if ($investor->investor->organization)
                        <p>{{ $investor->investor->organization->name }}</p>
                    @endif
                    <p class="text-sm text-base-content/70">{{ $investor->investor->email }}</p>
                </div>

                <!-- Meeting Information -->
                <div class="mb-6">
                    <p class="text-sm">
                        <span class="font-medium">{{ __('Meeting Date') }}:</span>
                        {{ $meeting->timeSlot->start_time->format('d/m/Y') }}
                    </p>
                    <p class="text-sm">
                        <span class="font-medium">{{ __('Meeting Time') }}:</span>
                        {{ $meeting->timeSlot->start_time->format('H:i') }} -
                        {{ $meeting->timeSlot->end_time->format('H:i') }}
                    </p>
                    <p class="text-sm">
                        <span class="font-medium">{{ __('Room') }}:</span>
                        {{ $meeting->room->name ?? __('Not assigned') }}
                    </p>
                </div>

                <!-- QR Code -->
                <div class="my-4 bg-white p-6 rounded-lg shadow-inner">
                    <img id="qrcode"
                        src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($investor->investor->qr_code) }}&amp;size=300x300"
                        class="mx-auto" alt="QR Code" />
                </div>

                <div class="flex flex-col items-center gap-3">
                    <div class="badge badge-neutral">
                        {{ __('QR Code ID') }}: <span class="font-mono">{{ $investor->investor->qr_code }}</span>
                    </div>
                    <button id="copyQrCode" class="btn btn-sm btn-accent rounded-full">
                        <x-heroicon-s-document-duplicate class="size-4" />
                        {{ __('Copy QR Code') }}
                    </button>
                </div>

                <div class="mt-6">
                    <p class="text-sm text-base-content/70">
                        {{ __('This QR code belongs to the user and can be used to check in to any meeting they are registered for.') }}</p>
                </div>

                <!-- Print only instructions -->
                <div class="hidden print:block mt-4 border-t pt-4">
                    <p class="text-sm">
                        {{ __('Please bring this QR code with you to the meeting for quick check-in.') }}</p>
                </div>

                <div class="mt-6 flex gap-2 print:hidden">
                    <a href="{{ route('admin.meetings.investors', $meeting) }}" class="btn btn-ghost">
                        {{ __('Back to Investors') }}
                    </a>
                    <a href="{{ route('admin.meetings.show', $meeting) }}" class="btn btn-ghost">
                        {{ __('Back to Meeting') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const copyQrCodeButton = document.getElementById('copyQrCode');
            const qrCodeValue = '{{ $investor->investor->qr_code }}';

            copyQrCodeButton.addEventListener('click', function() {
                navigator.clipboard.writeText(qrCodeValue)
                    .then(() => {
                        // Change button text temporarily to show success
                        const originalText = copyQrCodeButton.innerHTML;
                        copyQrCodeButton.innerHTML = `
                            <x-heroicon-s-check class="size-4" />
                            {{ __('Copied!') }}
                        `;
                        copyQrCodeButton.classList.remove('btn-accent');
                        copyQrCodeButton.classList.add('btn-success');

                        setTimeout(() => {
                            copyQrCodeButton.innerHTML = originalText;
                            copyQrCodeButton.classList.remove('btn-success');
                            copyQrCodeButton.classList.add('btn-accent');
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Failed to copy text: ', err);
                        alert('{{ __('Failed to copy QR code') }}');
                    });
            });
        });
    </script>

    <style>
        @media print {

            header,
            footer,
            .btn,
            .breadcrumbs {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
</x-app-layout>
