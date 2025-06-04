<x-app-layout>
    <section class="space-y-6">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('investor.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li><a href="{{ route('investor.issuers.index') }}" class="text-primary">{{ __('Issuers Directory') }}</a>
                </li>
                <li><a href="{{ route('investor.issuers.show', $issuer->id) }}"
                        class="text-primary">{{ __('Issuer Profile') }}</a></li>
                <li>{{ __('Request Meeting') }}</li>
            </ul>
        </div>

        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-primary">
                    {{ __('Request a Meeting with') }} {{ $issuer->first_name }} {{ $issuer->name }}
                </h1>
            </div>
        </div>

        @if (session('error'))
            <div role="alert" class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div role="alert" class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Issuer Information -->
            <div class="md:col-span-1">
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Issuer Information') }}</h2>
                        <div class="space-y-4">
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

                            @if ($issuer->organization && $issuer->organization->country)
                                <div>
                                    <div class="text-sm font-medium text-base-content/70">{{ __('Country') }}</div>
                                    <div class="mt-1 text-sm text-base-content">
                                        {{ $issuer->organization->country->name }}
                                    </div>
                                </div>
                            @endif

                            <a href="{{ route('investor.issuers.show', $issuer->id) }}"
                                class="btn btn-outline w-full mt-4">
                                {{ __('Go to Issuer Profile') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meeting Request Form -->
            <div class="md:col-span-2">
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Request Meeting') }}</h2>
                        <form action="{{ route('investor.meetings.request') }}" method="POST"
                            id="meeting-request-form">
                            @csrf
                            <input type="hidden" name="issuer_id" value="{{ $issuer->id }}">
                            <input type="hidden" name="time_slot_id" id="time_slot_id"
                                value="{{ old('time_slot_id') }}">

                            <div class="space-y-6">
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">{{ __('1. Select a Time Slot') }}</legend>
                                    <div class="text-sm text-base-content/70 mb-4">
                                        {{ __('Please select an available time slot for your meeting.') }}</div>

                                    <div class="space-y-6">
                                        @foreach ($groupedTimeSlots as $date => $slots)
                                            <div>
                                                <h3 class="text-md font-semibold text-primary mb-3">
                                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                                </h3>
                                                <div class="flex flex-wrap gap-3">
                                                    @foreach ($slots as $timeSlot)
                                                        <div class="time-slot-card btn rounded-full {{ old('time_slot_id') == $timeSlot->id ? 'btn-primary' : '' }}"
                                                            data-time-slot-id="{{ $timeSlot->id }}">
                                                            <div class="text-center">
                                                                {{ $timeSlot->start_time->format('H:i') }} -
                                                                {{ $timeSlot->end_time->format('H:i') }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            {{-- @if (!$loop->last)
                                                <div class="divider"></div>
                                            @endif --}}
                                        @endforeach
                                    </div>
                                    @error('time_slot_id')
                                        <p class="label text-error text-sm">{{ $message }}</p>
                                    @enderror
                                </fieldset>
                                
                                <div class="divider"></div>

                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">{{ __('2. Ask a Question (Optional)') }}</legend>
                                    <div class="text-sm text-base-content/70 mb-4">
                                        {{ __('You can include a question for the issuer that will be addressed during the meeting.') }}
                                    </div>

                                    <textarea name="question" id="question" rows="4" class="textarea textarea-bordered w-full"
                                        placeholder="{{ __('Type your question here...') }}">{{ old('question') }}</textarea>
                                    @error('question')
                                        <p class="label text-error text-sm">{{ $message }}</p>
                                    @enderror
                                </fieldset>

                                <div class="flex justify-end mt-6">
                                    <button type="submit" id="submit-button" class="btn btn-primary" disabled>
                                        {{ __('Submit Meeting Request') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeSlotCards = document.querySelectorAll('.time-slot-card');
            const timeSlotIdInput = document.getElementById('time_slot_id');
            const submitButton = document.getElementById('submit-button');

            // Function to update selected time slot
            function updateSelectedTimeSlot() {
                // Enable submit button only if a time slot is selected
                submitButton.disabled = !timeSlotIdInput.value;
            }

            // Function to select a time slot
            function selectTimeSlot(card) {
                // Deselect all cards
                timeSlotCards.forEach(function(c) {
                    c.classList.remove('btn-primary');
                });

                // Select the clicked card
                card.classList.add('btn-primary');

                // Update the hidden input
                timeSlotIdInput.value = card.dataset.timeSlotId;

                // Update submit button state
                updateSelectedTimeSlot();
            }

            // Add click event to each time slot card
            timeSlotCards.forEach(function(card) {
                card.addEventListener('click', function() {
                    selectTimeSlot(this);

                    // Add animation effect
                    this.classList.add('ring', 'ring-primary/30');
                    setTimeout(() => this.classList.remove('ring', 'ring-primary/30'), 800);
                });
            });

            // Pre-select time slot if one was previously selected (e.g., form validation error)
            if (timeSlotIdInput.value) {
                const selectedCard = document.querySelector(
                    `.time-slot-card[data-time-slot-id="${timeSlotIdInput.value}"]`);
                if (selectedCard) {
                    selectTimeSlot(selectedCard);
                }
            }

            // Initialize submit button state
            updateSelectedTimeSlot();

            // Form submission animation
            const form = document.getElementById('meeting-request-form');
            form.addEventListener('submit', function() {
                if (timeSlotIdInput.value) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '{{ __('Submitting...') }}';
                    submitButton.classList.add('opacity-75');
                    return true;
                }
                return false;
            });
        });
    </script>
</x-app-layout>
