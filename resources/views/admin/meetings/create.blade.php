<x-app-layout>
    <section class="space-y-6">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li><a href="{{ route('admin.meetings.index') }}" class="text-primary">{{ __('Meetings') }}</a></li>
                <li>{{ __('Create') }}</li>
            </ul>
        </div>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-primary">
                    {{ __('Create New Meeting') }}
                </h1>
            </div>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body">
                <form action="{{ route('admin.meetings.store') }}" method="POST">
                    @csrf

                    {{-- Meeting Basic Information Section --}}
                    <section class="mb-6 border-b border-base-200 pb-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <h2 class="text-xl font-medium text-primary mb-4">
                                {{ __('Meeting Information') }}
                            </h2>
                            <p>
                                {{ __('Select the issuer and venue for this meeting.') }}
                            </p>
                        </div>

                        <div class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-x-8">
                            <!-- Issuer Selection -->
                            <fieldset class="fieldset mb-4 sm:col-span-1">
                                <legend class="fieldset-legend">{{ __('Issuer') }} *</legend>
                                <select id="issuer_id" name="issuer_id" class="select select-bordered w-full" required>
                                    <option value="" disabled selected>{{ __('-- Select --') }}</option>
                                    @foreach ($issuers as $issuer)
                                        <option value="{{ $issuer->id }}"
                                            {{ old('issuer_id') == $issuer->id ? 'selected' : '' }}>
                                            {{ $issuer->name . ' ' . $issuer->first_name }}
                                            @if ($issuer->organization)
                                                ({{ $issuer->organization->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('issuer_id')
                                    <p class="label text-error text-sm">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <!-- Room Selection -->
                            <fieldset class="fieldset mb-4 sm:col-span-1">
                                <legend class="fieldset-legend">{{ __('Room') }}</legend>
                                <select id="room_id" name="room_id" class="select select-bordered w-full">
                                    <option value="">{{ __('-- No Room --') }}</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->name }} (Capacity: {{ $room->capacity }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <p class="label text-error text-sm">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <!-- Meeting Type (One-on-One or Group) -->
                            <fieldset class="fieldset mb-4 sm:col-span-1">
                                <legend class="fieldset-legend">{{ __('Meeting Type') }} *</legend>
                                <div class="flex items-center space-x-6 mt-2">
                                    <div class="flex items-center">
                                        <input id="is_one_on_one_1" name="is_one_on_one" type="radio"
                                            class="radio radio-primary" value="1"
                                            {{ old('is_one_on_one') == '1' ? 'checked' : '' }}>
                                        <label for="is_one_on_one_1" class="ml-2 block text-sm">
                                            {{ __('One-on-One (single investor)') }}
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="is_one_on_one_0" name="is_one_on_one" type="radio"
                                            class="radio radio-primary" value="0"
                                            {{ old('is_one_on_one') == '0' || old('is_one_on_one') === null ? 'checked' : '' }}>
                                        <label for="is_one_on_one_0" class="ml-2 block text-sm">
                                            {{ __('Group (multiple investors)') }}
                                        </label>
                                    </div>
                                </div>
                                @error('is_one_on_one')
                                    <p class="label text-error text-sm">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            <!-- Meeting Status -->
                            <fieldset class="fieldset mb-4 sm:col-span-1">
                                <legend class="fieldset-legend">{{ __('Status') }} *</legend>
                                <select id="status" name="status" class="select select-bordered w-full" required>
                                    @foreach (\App\Enums\MeetingStatus::cases() as $meetingStatus)
                                        <option value="{{ $meetingStatus->value }}"
                                            {{ old('status') == $meetingStatus->value ? 'selected' : '' }}>
                                            {{ __($meetingStatus->label()) }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="label text-error text-sm">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>
                    </section>

                    {{-- Time Slot Section --}}
                    <section class="mb-6 border-b border-base-200 pb-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <h2 class="text-xl font-medium text-primary mb-4">
                                {{ __('Schedule') }}
                            </h2>
                            <p>
                                {{ __('Select a date and time for the meeting.') }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <!-- Time Slot Selection -->
                            <fieldset class="fieldset mb-4">
                                <legend class="fieldset-legend">{{ __('Time Slot') }} *</legend>

                                <!-- Hidden input to store selected time slot ID -->
                                <input type="hidden" id="time_slot_id" name="time_slot_id"
                                    value="{{ old('time_slot_id') }}">

                                <div id="time_slot_container">
                                    <p id="no_issuer_message"
                                        class="italic text-base-content/70 {{ old('issuer_id') ? 'hidden' : '' }}">
                                        {{ __('Please select an issuer first to see available time slots.') }}
                                    </p>

                                    <p id="no_slots_message" class="italic text-base-content/70 hidden">
                                        {{ __('No time slots available for this issuer.') }}
                                    </p>

                                    <div id="time_slots_grid" class="space-y-6 {{ old('issuer_id') ? '' : 'hidden' }}">
                                        @foreach ($timeSlots->groupBy(function ($timeSlot) {
        return $timeSlot->date->format('Y-m-d');
    }) as $date => $dateTimes)
                                            <div>
                                                <h3 class="text-md font-semibold text-primary mb-3">
                                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                                </h3>
                                                <div class="flex flex-wrap gap-3">
                                                    @foreach ($dateTimes as $timeSlot)
                                                        <div class="time-slot-card btn rounded-full {{ old('time_slot_id') == $timeSlot->id ? 'btn-primary' : '' }}"
                                                            data-time-slot-id="{{ $timeSlot->id }}"
                                                            data-issuer-id="{{ $timeSlot->user_id }}"
                                                            data-issuer-name="{{ $timeSlot->user->name }}">
                                                            <div class="text-center">
                                                                {{ $timeSlot->start_time->format('H:i') }} -
                                                                {{ $timeSlot->end_time->format('H:i') }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('time_slot_id')
                                    <p class="label text-error text-sm">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>
                    </section>

                    {{-- Participants Section --}}
                    <section class="mb-6 border-b border-base-200 pb-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <h2 class="text-xl font-medium text-primary mb-4">
                                {{ __('Participants') }}
                            </h2>
                            <p>
                                {{ __('Select investors to invite to this meeting.') }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <!-- Investors Selection -->
                            <fieldset class="fieldset mb-4">
                                <legend class="fieldset-legend">{{ __('Investors') }} *</legend>
                                <x-multi-select name="investor_ids" id="investor_ids" :options="$investors"
                                    :selected="old('investor_ids', [])" :placeholder="__('-- Select Investors --')" :required="true" :isMultiple="true"
                                    :allowSearch="true" nameKey="name" valueKey="id" organizationKey="organization.name"
                                    emailKey="email" />
                                <input type="hidden" name="investor_ids_sentinel" value="1">
                                @error('investor_ids')
                                    <p class="label text-error text-sm">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>
                    </section>

                    {{-- Additional Information Section --}}
                    <section class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <h2 class="text-xl font-medium text-primary mb-4">
                                {{ __('Additional Information') }}
                            </h2>
                            <p>
                                {{ __('Add any notes or additional details about this meeting.') }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <!-- Notes -->
                            <fieldset class="fieldset mb-4">
                                <legend class="fieldset-legend">{{ __('Notes (optional)') }}</legend>
                                <textarea id="notes" name="notes" rows="4" class="textarea textarea-bordered w-full">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="label text-error text-sm">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>
                    </section>

                    <div class="mt-8 flex justify-end gap-2">
                        <a href="{{ route('admin.meetings.index') }}" class="btn btn-ghost">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            {{ __('Create Meeting') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const oneOnOneRadios = document.querySelectorAll('input[name="is_one_on_one"]');
            const issuerSelect = document.getElementById('issuer_id');
            const timeSlotContainer = document.getElementById('time_slot_container');
            const timeSlotGrid = document.getElementById('time_slots_grid');
            const noIssuerMessage = document.getElementById('no_issuer_message');
            const noSlotsMessage = document.getElementById('no_slots_message');
            const timeSlotInput = document.getElementById('time_slot_id');
            const timeSlotCards = document.querySelectorAll('.time-slot-card');
            const submitBtn = document.getElementById('submit-btn');

            // Function to filter time slots based on selected issuer
            function filterTimeSlots() {
                const selectedIssuerId = issuerSelect.value;

                // Add visual feedback during load
                if (selectedIssuerId) {
                    timeSlotContainer.classList.add('opacity-70');
                    setTimeout(() => timeSlotContainer.classList.remove('opacity-70'), 500);
                }

                if (!selectedIssuerId) {
                    noIssuerMessage.classList.remove('hidden');
                    noSlotsMessage.classList.add('hidden');
                    timeSlotGrid.classList.add('hidden');
                    timeSlotInput.value = ''; // Clear selected time slot
                    return;
                }

                noIssuerMessage.classList.add('hidden');
                let matchFound = false;

                // Masquer d'abord tous les groupes de date
                const dateGroups = timeSlotGrid.querySelectorAll('.mb-6');
                dateGroups.forEach(dateGroup => {
                    dateGroup.classList.add('hidden');
                });

                timeSlotCards.forEach(card => {
                    if (card.dataset.issuerId === selectedIssuerId) {
                        card.classList.remove('hidden');
                        // Afficher le groupe de date parent
                        const dateGroup = card.closest('.mb-6');
                        dateGroup.classList.remove('hidden');
                        matchFound = true;
                    } else {
                        card.classList.add('hidden');

                        // If this was the selected card, deselect it since it's now hidden
                        if (card.dataset.timeSlotId === timeSlotInput.value) {
                            card.classList.remove('outline-2', '-outline-offset-2', 'outline-blue-600');
                            timeSlotInput.value = '';
                        }
                    }
                });

                if (matchFound) {
                    noSlotsMessage.classList.add('hidden');
                    timeSlotGrid.classList.remove('hidden');
                } else {
                    noSlotsMessage.classList.remove('hidden');
                    timeSlotGrid.classList.add('hidden');
                    timeSlotInput.value = ''; // Clear selected time slot
                }
            }

            // Apply filter when issuer changes
            issuerSelect.addEventListener('change', function() {
                issuerSelect.classList.add('ring-2', 'ring-blue-400');
                setTimeout(() => issuerSelect.classList.remove('ring-2', 'ring-blue-400'), 800);
                filterTimeSlots();
            });

            // Initial filter based on preselected issuer
            if (issuerSelect.value) {
                filterTimeSlots();
            }

            // Handle time slot card selection
            timeSlotCards.forEach(card => {
                card.addEventListener('click', function() {
                    if (this.classList.contains('hidden')) return;

                    // Deselect all cards
                    timeSlotCards.forEach(c => {
                        c.classList.remove('btn-primary');
                    });

                    // Select clicked card
                    this.classList.add('btn-primary');
                    timeSlotInput.value = this.dataset.timeSlotId;
                });
            });

            // Function to enforce one-on-one meeting rules for multi-select component
            window.addEventListener('multi-select-change', function(e) {
                if (e.detail.name !== 'investor_ids') return;

                const isOneOnOne = document.getElementById('is_one_on_one_1').checked;
                const selectedInvestorIds = e.detail.selectedValues;

                if (isOneOnOne && selectedInvestorIds.length > 1) {
                    // Keep only the last selected value
                    const lastSelectedId = selectedInvestorIds[selectedInvestorIds.length - 1];

                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: {
                            type: 'warning',
                            message: '{{ __('A One-on-One meeting can only have one investor.') }}'
                        }
                    }));

                    // Update the selected values to only have one
                    e.detail.selectedValues = [lastSelectedId];

                    // Dispatch a custom event to force the multi-select component to update
                    window.dispatchEvent(new CustomEvent('multi-select-enforce-limit', {
                        detail: {
                            name: 'investor_ids',
                            selectedValues: [lastSelectedId]
                        }
                    }));
                }
            });

            // Check on each meeting type change
            oneOnOneRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const radioContainer = this.closest('div.flex.items-center');
                    radioContainer.classList.add('ring-2', 'ring-blue-400', 'rounded-md');
                    setTimeout(() => radioContainer.classList.remove('ring-2', 'ring-blue-400',
                        'rounded-md'), 800);

                    // If switching to one-on-one, check if we need to limit selection
                    if (this.value === '1') {
                        const event = new CustomEvent('multi-select-change', {
                            detail: {
                                name: 'investor_ids',
                                selectedValues: Array.from(document.querySelectorAll(
                                    'input[name="investor_ids[]"]:checked')).map(cb =>
                                    parseInt(cb.value))
                            }
                        });
                        window.dispatchEvent(event);
                    }
                });
            });

            // Form submission effect
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerText = '{{ __('Creating...') }}';
                submitBtn.classList.add('animate-pulse');
            });
        });
    </script>
</x-app-layout>
