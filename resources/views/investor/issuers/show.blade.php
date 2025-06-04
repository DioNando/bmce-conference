<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('investor.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('investor.issuers.index') }}" class="text-primary">{{ __('Issuers') }}</a></li>
            <li>{{ $issuer->first_name }} {{ $issuer->name }}</li>
        </ul>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">
            {{ __('Issuer Profile') }}
        </h1>
    </div>

    <section class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Issuer Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Issuer Information') }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Full Name') }}</div>
                                <div class="mt-1 text-lg font-medium text-base-content">
                                    {{ $issuer->first_name }} {{ $issuer->name }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Position') }}</div>
                                <div class="mt-1 text-sm text-base-content">
                                    {{ $issuer->position ?? 'Position not specified' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Organization') }}</div>
                                <div class="mt-1 text-sm text-base-content">
                                    {{ $issuer->organization->name ?? 'No Organization' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Email') }}</div>
                                <div class="mt-1 text-sm text-base-content">
                                    {{ $issuer->email }}
                                </div>
                            </div>

                            @if ($issuer->phone)
                                <div>
                                    <div class="text-sm font-medium text-base-content/70">{{ __('Phone') }}</div>
                                    <div class="mt-1 text-sm text-base-content">
                                        {{ $issuer->phone }}
                                    </div>
                                </div>
                            @endif

                            @if ($issuer->organization && $issuer->organization->country)
                                <div>
                                    <div class="text-sm font-medium text-base-content/70">{{ __('Country') }}</div>
                                    <div class="mt-1 text-sm text-base-content">
                                        {{ $issuer->organization->country->name }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($issuer->organization && $issuer->organization->description)
                            <div class="mt-6 pt-4 border-t border-base-200">
                                <div class="text-sm font-medium text-base-content/70">
                                    {{ __('Organization Description') }}
                                </div>
                                <div class="mt-2 text-sm text-base-content prose max-w-none">
                                    {{ $issuer->organization->description }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Actions') }}</h2>
                        <div class="space-y-3 mt-4">
                            <a href="#available-slots" class="btn btn-primary w-full">
                                {{ __('View Available Time Slots') }}
                            </a>
                            <a href="{{ route('investor.issuers.index') }}" class="btn btn-outline w-full">
                                {{ __('Back to Issuers Directory') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Time Slots Section -->
        <div id="available-slots">
            <div class="card card-bordered bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-primary">{{ __('Available Time Slots') }}</h2>

                    @if ($availableTimeSlots->count() > 0)
                        <div class="space-y-6">
                            @php
                                $groupedTimeSlots = $availableTimeSlots->groupBy(function ($timeSlot) {
                                    return $timeSlot->date->format('Y-m-d');
                                });
                            @endphp

                            @foreach ($groupedTimeSlots as $date => $slots)
                                <div class="mb-6">
                                    <h3 class="text-md font-semibold text-primary mb-3">
                                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                    </h3>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach ($slots as $timeSlot)
                                            <div
                                                class="time-slot-card rounded-full border border-base-300 p-3 pr-4">
                                                <div class="text-center h-full flex gap-2 items-center justify-center">
                                                    <button type="button"
                                                        class="btn btn-circle btn-primary btn-sm request-meeting-btn hover:scale-110 transition-transform duration-200 ease-in-out"
                                                        @click="$dispatch('open-modal', 'request-meeting-modal')"
                                                        data-time-slot-id="{{ $timeSlot->id }}"
                                                        data-issuer-id="{{ $issuer->id }}"
                                                        data-time-slot-text="{{ $timeSlot->start_time->format('H:i') }} - {{ $timeSlot->end_time->format('H:i') }}">
                                                        <x-heroicon-s-calendar class="size-4" />
                                                    </button>
                                                    <div class="text-base font-semibold text-base-content">
                                                        {{ $timeSlot->start_time->format('H:i') }} -
                                                        {{ $timeSlot->end_time->format('H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <hr class="my-4 border-base-200">
                                @endif
                            @endforeach
                        </div>

                        <!-- Modal for request meeting with question - using x-modal component -->
                        <x-modal name="request-meeting-modal" :show="false" focusable maxWidth="md">
                            <form action="{{ route('investor.meetings.request') }}" method="POST" class="p-6">
                                @csrf
                                <input type="hidden" name="time_slot_id" id="timeSlotIdInput">
                                <input type="hidden" name="issuer_id" id="issuerIdInput">

                                <h3 class="font-medium text-lg text-primary mb-4">
                                    {{ __('Request Meeting') }}
                                </h3>

                                <p class="text-base-content/70 text-sm">
                                    {{ __('You are requesting a meeting with') }} <span
                                        class="font-medium">{{ $issuer->first_name }} {{ $issuer->name }}</span>
                                </p>
                                <p class="text-base-content/70 text-sm mt-1 mb-4">
                                    {{ __('Time slot:') }} <span id="selectedTimeSlot" class="font-medium"></span>
                                </p>

                                <fieldset class="fieldset mt-6">
                                    <legend class="fieldset-legend">{{ __('Your Question (Optional)') }}</legend>
                                    <textarea id="question" name="question" rows="4" class="textarea textarea-bordered w-full"
                                        placeholder="{{ __('Do you have a question for the issuer? Type it here...') }}"></textarea>
                                </fieldset>

                                <div class="mt-6 flex justify-end gap-3">
                                    <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                                        {{ __('Cancel') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit Request') }}
                                    </button>
                                </div>
                            </form>
                        </x-modal>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-base-content/50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-base-content">{{ __('No available time slots') }}
                            </h3>
                            <p class="mt-1 text-sm text-base-content/70">
                                {{ __('This issuer has no available time slots at the moment.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const requestButtons = document.querySelectorAll('.request-meeting-btn');
            const timeSlotIdInput = document.getElementById('timeSlotIdInput');
            const issuerIdInput = document.getElementById('issuerIdInput');
            const selectedTimeSlotText = document.getElementById('selectedTimeSlot');

            // Set up event handlers for the request buttons
            requestButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const timeSlotId = this.dataset.timeSlotId;
                    const issuerId = this.dataset.issuerId;
                    const timeSlotText = this.dataset.timeSlotText;

                    timeSlotIdInput.value = timeSlotId;
                    issuerIdInput.value = issuerId;
                    selectedTimeSlotText.textContent = timeSlotText;
                });
            });
        });
    </script>
</x-app-layout>
