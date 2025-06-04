<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('issuer.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li>{{ __('Availability') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Manage Your Availability') }}
            </h3>
            <div class="flex gap-3">
                <form action="{{ route('issuer.generate-time-slots') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <x-heroicon-s-clock class="size-4" />
                        <span> {{ __('Regenerate TimeSlots') }}</span>
                    </button>
                </form>
            </div>
        </div>

        <div role="alert" class="alert alert-info">
            <x-heroicon-o-information-circle class="size-7" />
            <div>
                <p class="text-base-content/80">
                    {{ __('Please indicate your availability for meetings during the conference. Check the boxes for the time slots when you are available. Investors will only be able to request meetings during your available time slots.') }}
                </p>
                <p class="text-base-content/80 mt-2">
                    <strong>{{ __('Note:') }}</strong>
                    {{ __('You cannot mark a time slot as unavailable if meetings are already scheduled for that time.') }}
                </p>
            </div>
        </div>

        @if (session('success') || request()->has('success'))
            <div role="alert" class="alert alert-success mb-4">
                <span>{{ session('success') ?? request()->get('success') }}</span>
            </div>
        @endif

        @if (session('error') || request()->has('error'))
            <div role="alert" class="alert alert-error mb-4">
                <span>{{ session('error') ?? request()->get('error') }}</span>
            </div>
        @endif

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body" id="schedule-management">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-primary">{{ __('Time Slots') }}</h2>
                    <p class="text-sm text-base-content/70">{{ __('Select your availability for each day') }}</p>
                </div>

                @forelse($timeSlotsByDate as $date => $dayData)
                    <div class="flex flex-col gap-4 timeslot-day" data-date="{{ $date }}">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-warning">{{ $dayData['formatted_date'] }}</h2>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="toggle-all-day toggle toggle-warning checkbox-md" />
                                    <span class="ml-2 text-sm text-base-content/70">{{ __('Select All') }}</span>
                                </label>
                                <button class="btn btn-warning save-day-button" data-date="{{ $date }}"
                                    disabled>
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-6 card card-bordered bg-base-200">
                            @foreach ($dayData['time_slots'] as $timeSlot)
                                <div class="card card-bordered card-sm shadow-sm hover:shadow-lg cursor-pointer time-slot-card {{ $timeSlot->availability ? 'bg-warning/10' : 'bg-base-100' }} {{ $timeSlot->meetings->count() > 0 ? 'has-meetings' : '' }}"
                                    data-timeslot-id="{{ $timeSlot->id }}"
                                    {{ $timeSlot->meetings->count() > 0 && !$timeSlot->availability ? 'data-disabled="true"' : '' }}>
                                    <div class="card-body p-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium">
                                                {{ Carbon\Carbon::parse($timeSlot->start_time)->format('H:i') }}
                                                -
                                                {{ Carbon\Carbon::parse($timeSlot->end_time)->format('H:i') }}
                                            </span>
                                            <label class="flex items-center cursor-pointer">
                                                <span
                                                    class="mr-2 text-xs text-base-content/70">{{ __('Available') }}</span>
                                                <input type="checkbox"
                                                    class="toggle toggle-warning checkbox-md toggle-availability"
                                                    data-timeslot-id="{{ $timeSlot->id }}"
                                                    data-initial="{{ $timeSlot->availability ? 'true' : 'false' }}"
                                                    {{ $timeSlot->availability ? 'checked' : '' }}
                                                    {{ $timeSlot->meetings->count() > 0 && !$timeSlot->availability ? 'disabled' : '' }} />
                                            </label>
                                        </div>

                                        <div class="mt-2 flex items-center">
                                            @if ($timeSlot->meetings->count() > 0)
                                                <span class="badge badge-error gap-1">
                                                    {{ $timeSlot->meetings->count() }} {{ __('meetings scheduled') }}
                                                </span>
                                            @else
                                                <span class="badge badge-ghost badge-sm gap-1">
                                                    {{ __('No meetings') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if (!$loop->last)
                            <div class="divider"></div>
                        @endif
                    </div>
                    {{-- <div class="divider"></div> --}}
                @empty
                    <div class="text-center">
                        <div role="alert" class="alert alert-warning">
                            <span>{{ __('No time slots found. Please regenerate time slots.') }}</span>
                        </div>
                    </div>
                @endforelse

                @if (count($timeSlotsByDate) > 0)
                    <div class="mt-6 flex justify-end">
                        <button id="save-all-button" class="btn btn-warning">
                            {{ __('Save All Changes') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success') || urlParams.has('error')) {
                // Clear URL parameters after displaying messages
                const url = new URL(window.location);
                url.searchParams.delete('success');
                url.searchParams.delete('error');
                window.history.replaceState({}, '', url);
            }
            // Permettre de cliquer sur la carte entière pour activer/désactiver le checkbox
            document.querySelectorAll('.time-slot-card').forEach(function(card) {
                card.addEventListener('click', function(event) {
                    // Ne pas déclencher si on a cliqué directement sur le checkbox ou le label
                    if (event.target.matches('input[type="checkbox"]') ||
                        event.target.closest('label') ||
                        event.target.tagName === 'LABEL') {
                        return;
                    }

                    // Trouver le checkbox dans cette carte
                    const checkbox = this.querySelector('.toggle-availability');

                    // Ne pas activer si le checkbox est désactivé
                    if (!checkbox.disabled) {
                        checkbox.checked = !checkbox.checked;

                        // Déclencher l'événement change manuellement pour activer tous les listeners existants
                        checkbox.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });
            });

            // Marquer les changements dans l'interface
            document.querySelectorAll('.toggle-availability').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const parentCard = this.closest('.card');
                    const initialState = this.dataset.initial === 'true';
                    const currentState = this.checked;

                    // Si l'état a changé par rapport à l'état initial
                    if (initialState !== currentState) {
                        parentCard.classList.add('outline');
                        if (currentState) {
                            parentCard.classList.add('outline-warning'); // Ajout de disponibilité
                            parentCard.classList.remove('outline-error');
                            // Mise à jour visuelle de l'arrière-plan
                            parentCard.classList.remove('bg-base-100');
                            parentCard.classList.add('bg-warning/10');
                        } else {
                            parentCard.classList.add('outline-error'); // Retrait de disponibilité
                            parentCard.classList.remove('outline-warning');
                            // Mise à jour visuelle de l'arrière-plan
                            parentCard.classList.remove('bg-warning/10');
                            parentCard.classList.add('bg-base-100');
                        }
                    } else {
                        parentCard.classList.remove('outline', 'outline-warning',
                            'outline-error'); // Retour à l'état initial
                    }

                    // Activer le bouton de sauvegarde pour cette journée
                    const daySection = this.closest('.timeslot-day');
                    const saveButton = daySection.querySelector('.save-day-button');
                    saveButton.disabled = false;
                    saveButton.classList.add('animate-pulse');
                });
            });

            // Gérer les changements pour "Sélectionner tout"
            document.querySelectorAll('.toggle-all-day').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const daySection = this.closest('.timeslot-day');
                    const timeSlotCheckboxes = daySection.querySelectorAll(
                        '.toggle-availability:not([disabled])');
                    const isChecked = this.checked;

                    // Mettre à jour l'état visuel des cases sans envoyer de requête
                    timeSlotCheckboxes.forEach(function(tsCheckbox) {
                        tsCheckbox.checked = isChecked;

                        const parentCard = tsCheckbox.closest('.card');
                        const initialState = tsCheckbox.dataset.initial === 'true';

                        if (initialState !== isChecked) {
                            parentCard.classList.add('outline');
                            if (isChecked) {
                                parentCard.classList.add(
                                    'outline-warning'); // Ajout de disponibilité
                                parentCard.classList.remove('outline-error');
                                // Mise à jour visuelle de l'arrière-plan
                                parentCard.classList.remove('bg-base-100');
                                parentCard.classList.add('bg-warning/10');
                            } else {
                                parentCard.classList.add(
                                    'outline-error'); // Retrait de disponibilité
                                parentCard.classList.remove('outline-warning');
                                // Mise à jour visuelle de l'arrière-plan
                                parentCard.classList.remove('bg-warning/10');
                                parentCard.classList.add('bg-base-100');
                            }
                        } else {
                            parentCard.classList.remove('outline', 'outline-warning',
                                'outline-error');
                        }
                    });

                    // Activer le bouton de sauvegarde pour cette journée
                    const saveButton = daySection.querySelector('.save-day-button');
                    saveButton.disabled = false;
                    saveButton.classList.add('animate-pulse');
                });
            });

            // Gérer la sauvegarde par jour
            document.querySelectorAll('.save-day-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    const date = this.dataset.date;
                    const daySection = this.closest('.timeslot-day');

                    // Récupérer l'état de toutes les cases à cocher de ce jour
                    const timeslots = [];
                    daySection.querySelectorAll('.toggle-availability').forEach(function(checkbox) {
                        if (!checkbox.disabled) {
                            timeslots.push({
                                id: checkbox.closest('.time-slot-card').dataset
                                    .timeslotId,
                                availability: checkbox.checked
                            });
                        }
                    });

                    if (timeslots.length === 0) {
                        return;
                    }

                    // Animation de chargement
                    this.disabled = true;
                    const originalText = this.textContent;
                    this.textContent = '{{ __('Saving...') }}';
                    daySection.classList.add('opacity-70');

                    // Envoyer la requête AJAX
                    fetch(`/issuer/schedule/${date}/batch-update`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                timeslots: timeslots
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mettre à jour l'état initial des checkboxes
                                daySection.querySelectorAll('.toggle-availability').forEach(
                                    function(checkbox) {
                                        const card = checkbox.closest('.time-slot-card');
                                        const id = card.dataset.timeslotId;
                                        const updatedSlot = timeslots.find(slot => slot
                                            .id === id);

                                        if (updatedSlot) {
                                            checkbox.dataset.initial = updatedSlot
                                                .availability ? 'true' : 'false';
                                            card.classList.remove('outline',
                                                'outline-warning', 'outline-error');
                                        }
                                    });

                                // Rediriger avec un message de succès en session au lieu d'une notification JavaScript
                                const redirectUrl = new URL(window.location.href);
                                redirectUrl.searchParams.set('success',
                                    'Changes saved successfully!');
                                window.location.href = redirectUrl.toString();
                            } else {
                                // Rediriger avec un message d'erreur en session
                                const redirectUrl = new URL(window.location.href);
                                redirectUrl.searchParams.set('error', data.message ||
                                    '{{ __('An error occurred while saving. Please try again.') }}'
                                );
                                window.location.href = redirectUrl.toString();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Rediriger avec un message d'erreur en session
                            const redirectUrl = new URL(window.location.href);
                            redirectUrl.searchParams.set('error',
                                '{{ __('An error occurred while saving. Please try again.') }}'
                            );
                            window.location.href = redirectUrl.toString();
                        });
                });
            });

            // Sauvegarder tous les changements
            document.getElementById('save-all-button').addEventListener('click', function() {
                // Simuler le clic sur tous les boutons de sauvegarde par jour qui ne sont pas désactivés
                document.querySelectorAll('.save-day-button:not([disabled])').forEach(button => {
                    button.click();
                });
            });
        });
    </script>
</x-app-layout>
