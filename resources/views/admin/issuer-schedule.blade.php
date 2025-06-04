<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="text-primary">{{ __('Accounts') }}</a></li>
            <li><a href="{{ route('admin.users.show', $user) }}" class="text-primary">{{ $user->name }}</a></li>
            <li>{{ __('Schedule') }}</li>
        </ul>
    </div>
    <section class="space-y-6">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Schedule Management for') }} {{ $user->name }}
            </h3>
            <div class="flex gap-3">
                <form action="{{ route('admin.users.generate-time-slots', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <x-heroicon-s-clock class="size-4" />
                        <span> {{ __('Regenerate TimeSlots') }}</span>
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div role="alert" class="alert alert-success mb-4">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="alert alert-error mb-4">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body" id="schedule-management">
                @forelse($timeSlotsByDate as $date => $dayData)
                    <div class="flex flex-col gap-4 timeslot-day" data-date="{{ $date }}">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-primary">{{ $dayData['formatted_date'] }}</h2>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" class="toggle-all-day toggle toggle-primary toggle-md">
                                    <span class="ml-2 text-sm text-base-content/70">{{ __('Select All') }}</span>
                                </label>
                                <button class="btn btn-primary btn-sm save-day-button" data-date="{{ $date }}"
                                    data-user-id="{{ $user->id }}" disabled>
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-6 card card-bordered bg-base-200">
                            @foreach ($dayData['time_slots'] as $timeSlot)
                                <div class="card card-bordered card-sm shadow-sm hover:shadow-lg cursor-pointer time-slot-card {{ $timeSlot->availability ? 'bg-primary/10' : 'bg-base-100' }} {{ $timeSlot->meetings->count() > 0 ? 'has-meetings' : '' }}"
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
                                                    class="toggle-availability toggle toggle-primary toggle-md"
                                                    data-timeslot-id="{{ $timeSlot->id }}"
                                                    data-user-id="{{ $user->id }}"
                                                    data-initial="{{ $timeSlot->availability ? 'true' : 'false' }}"
                                                    {{ $timeSlot->availability ? 'checked' : '' }}
                                                    {{ $timeSlot->meetings->count() > 0 && !$timeSlot->availability ? 'disabled' : '' }}>
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
                    </div>
                    @if (!$loop->last)
                        <div class="divider"></div>
                    @endif
                @empty
                    <div class="text-center">
                        <div role="alert" class="alert alert-warning">
                            <span>{{ __('No time slots found for this issuer.') }}</span>
                        </div>
                    </div>
                @endforelse

                @if (count($timeSlotsByDate) > 0)
                    <div class="mt-6 flex justify-end">
                        <button id="save-all-button" class="btn btn-primary">
                            {{ __('Save All Changes') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                            parentCard.classList.add('outline-primary'); // Ajout de disponibilité
                            parentCard.classList.remove('outline-warning');
                        } else {
                            parentCard.classList.add('outline-warning'); // Retrait de disponibilité
                            parentCard.classList.remove('outline-primary');
                        }
                    } else {
                        parentCard.classList.remove('outline', 'outline-primary',
                            'outline-warning'); // Retour à l'état initial
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
                                    'outline-primary'); // Ajout de disponibilité
                                parentCard.classList.remove('outline-warning');
                            } else {
                                parentCard.classList.add(
                                    'outline-warning'); // Retrait de disponibilité
                                parentCard.classList.remove('outline-primary');
                            }
                        } else {
                            parentCard.classList.remove('outline', 'outline-primary',
                                'outline-warning'); // Retour à l'état initial
                        }

                        // Mise à jour visuelle de l'arrière-plan
                        if (isChecked) {
                            parentCard.classList.remove('bg-base-100');
                            parentCard.classList.add('bg-primary/10');
                        } else {
                            parentCard.classList.remove('bg-primary/10');
                            parentCard.classList.add('bg-base-100');
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
                    const userId = this.dataset.userId;
                    const daySection = this.closest('.timeslot-day');

                    // Récupérer l'état de toutes les cases à cocher de ce jour
                    const timeslots = [];
                    daySection.querySelectorAll('.toggle-availability').forEach(function(checkbox) {
                        if (!checkbox.disabled) {
                            timeslots.push({
                                id: checkbox.dataset.timeslotId,
                                availability: checkbox.checked
                            });
                        }
                    });

                    // Animation de chargement
                    this.disabled = true;
                    const originalText = this.textContent;
                    this.textContent = 'Saving...';
                    daySection.classList.add('opacity-70');

                    // Envoyer la requête AJAX
                    fetch(`/admin/users/${userId}/schedule/${date}/batch-update`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
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
                                        checkbox.dataset.initial = checkbox.checked ?
                                            'true' : 'false';
                                        const parentCard = checkbox.closest('.card');
                                        parentCard.classList.remove('outline',
                                            'outline-primary', 'outline-warning');
                                    });

                                // Rediriger avec un message de succès en session au lieu d'une notification JavaScript
                                const redirectUrl = new URL(window.location.href);
                                redirectUrl.searchParams.set('success',
                                    'Changes saved successfully!');
                                window.location.href = redirectUrl.toString();
                            } else {
                                // Rediriger avec un message d'erreur en session
                                const redirectUrl = new URL(window.location.href);
                                redirectUrl.searchParams.set('error', data.message);
                                window.location.href = redirectUrl.toString();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Rediriger avec un message d'erreur en session
                            const redirectUrl = new URL(window.location.href);
                            redirectUrl.searchParams.set('error',
                                'An error occurred while saving changes.');
                            window.location.href = redirectUrl.toString();
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.textContent = originalText;
                            daySection.classList.remove('opacity-70');
                            this.classList.remove('animate-pulse');
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
