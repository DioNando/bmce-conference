    @php
    // Get the nearest active event
    $nearestEvent = App\Models\Event::active()
        ->where('start_date', '>=', now())
        ->orderBy('start_date')
        ->first();

    // Fallback to any active event if there are no upcoming events
    if (!$nearestEvent) {
        $nearestEvent = App\Models\Event::active()->orderBy('start_date')->first();
    }
@endphp

<article class="flex flex-col justify-center items-center">
        <section id="event" class="flex-grow flex items-center justify-center">
            <div class="container mx-auto">
                <div class="flex flex-col gap-6 items-center justify-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-primary text-center">
                        {{ $nearestEvent ? $nearestEvent->name : __('4th Annual Investors Conference') }}</h1>
                    <p class="text-xl mb-4 text-secondary">
                        @if ($nearestEvent)
                            @if ($nearestEvent->start_date->format('Y-m-d') == $nearestEvent->end_date->format('Y-m-d'))
                                {{ $nearestEvent->start_date->format('j F Y') }}
                            @else
                                {{ $nearestEvent->start_date->format('j') }}-{{ $nearestEvent->end_date->format('j F Y') }}
                            @endif
                            <span class="mx-2">•</span>
                            {{ $nearestEvent->location }}
                        @else
                            {{ __('June 12-13, 2025') }} <span class="mx-2">•</span> {{ __('Casablanca, Morocco') }}
                        @endif
                    </p>

                    <div class="text-center w-fit max-w-3xl font-mono">
                        <div class="flex justify-center items-center gap-2 md:gap-4" id="countdown-timer">
                            <div class="countdown-el card card-border bg-primary/30 text-primary border-2 border-primary p-6 rounded-4xl shadow-lg text-3xl md:text-6xl font-bold">
                                <span style="--value:00;" id="countdown-days">00</span>
                                <div class="text-xs md:text-sm font-normal">{{ __('Days') }}</div>
                            </div>

                            <div class="text-2xl md:text-4xl text-primary">:</div>

                            <div class="countdown-el card card-border bg-primary/30 text-primary border-2 border-primary p-6 rounded-4xl shadow-lg text-3xl md:text-6xl font-bold">
                                <span style="--value:00;" id="countdown-hours">00</span>
                                <div class="text-xs md:text-sm font-normal">{{ __('Hours') }}</div>
                            </div>

                            <div class="text-2xl md:text-4xl text-primary">:</div>

                            <div class="countdown-el card card-border bg-primary/30 text-primary border-2 border-primary p-6 rounded-4xl shadow-lg text-3xl md:text-6xl font-bold">
                                <span style="--value:00;" id="countdown-minutes">00</span>
                                <div class="text-xs md:text-sm font-normal">{{ __('Minutes') }}</div>
                            </div>

                            <div class="text-2xl md:text-4xl text-primary">:</div>

                            <div class="countdown-el card card-border bg-primary/30 text-primary border-2 border-primary p-6 rounded-4xl shadow-lg text-3xl md:text-6xl font-bold">
                                <span style="--value:00;" id="countdown-seconds">00</span>
                                <div class="text-xs md:text-sm font-normal">{{ __('Seconds') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="py-12">
            <div class="container mx-auto">
                <h2 class="text-3xl font-bold text-center mb-3 text-secondary">
                    {{ __('Conference Features') }}</h2>

                @if ($nearestEvent && $nearestEvent->description)
                <div class="mb-8 text-center max-w-3xl mx-auto">
                    <p class="text-base-content">{{ $nearestEvent->description }}</p>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="card bg-base-200 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-xl text-primary">
                                {{ __('One-on-one Meetings') }}
                            </h3>
                            <p class="text-base-content">
                                {{ __('Connect directly with potential investors and partners through our streamlined meeting platform.') }}
                            </p>
                        </div>
                    </div>

                    <div class="card bg-base-200 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-xl text-primary">
                                {{ __('Expert Panels') }}
                            </h3>
                            <p class="text-base-content">
                                {{ __('Gain insights from industry leaders and market experts during our curated panel discussions.') }}
                            </p>
                        </div>
                    </div>

                    <div class="card bg-base-200 shadow-xl">
                        <div class="card-body">
                            <h3 class="card-title text-xl text-primary">
                                {{ __('Networking Events') }}
                            </h3>
                            <p class="text-base-content">
                                {{ __('Expand your professional network during our specially organized networking sessions and social events.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <script>
            // Set the date for the event from the nearest active event
            @if ($nearestEvent)
                const eventDate = new Date('{{ $nearestEvent->start_date->format('Y-m-d') }} 00:00:00').getTime();
            @else
                const eventDate = new Date('June 12, 2025 00:00:00').getTime();
            @endif

            // Fonction pour initialiser et mettre à jour le compteur
            function updateCountdown() {
                // Get current date and time
                const now = new Date().getTime();

                // Find the time remaining between now and the event date
                const timeRemaining = eventDate - now;

                // Calculate days, hours, minutes and seconds
                const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                // Display the result in the elements with corresponding ids
                const daysElement = document.getElementById('countdown-days');
                const hoursElement = document.getElementById('countdown-hours');
                const minutesElement = document.getElementById('countdown-minutes');
                const secondsElement = document.getElementById('countdown-seconds');

                // Set initial values directly to avoid showing 00
                daysElement.textContent = days.toString().padStart(2, '0');
                daysElement.style.setProperty('--value', days);

                hoursElement.textContent = hours.toString().padStart(2, '0');
                hoursElement.style.setProperty('--value', hours);

                minutesElement.textContent = minutes.toString().padStart(2, '0');
                minutesElement.style.setProperty('--value', minutes);

                secondsElement.textContent = seconds.toString().padStart(2, '0');
                secondsElement.style.setProperty('--value', seconds);

                return { timeRemaining, days, hours, minutes, seconds };
            }

            // Initialiser le compteur immédiatement
            const initialValues = updateCountdown();

            // Si le compteur est déjà terminé, afficher le message approprié
            if (initialValues.timeRemaining < 0) {
                @if ($nearestEvent)
                    // Vérifier si l'événement est terminé
                    const now = new Date().getTime();
                    const eventEndDate = new Date('{{ $nearestEvent->end_date->format('Y-m-d') }} 23:59:59').getTime();
                    if (now > eventEndDate) {
                        document.getElementById('countdown-timer').innerHTML =
                            "<div class='alert alert-info text-center'><span class='text-xl font-bold'>{{ __('The event has ended!') }}</span></div>";
                    } else {
                        document.getElementById('countdown-timer').innerHTML =
                            "<div class='alert alert-success text-center'><span class='text-xl font-bold'>{{ __('The event is ongoing!') }}</span></div>";
                    }
                @else
                    document.getElementById('countdown-timer').innerHTML =
                        "<div class='alert alert-success text-center'><span class='text-xl font-bold'>{{ __('The event is ongoing!') }}</span></div>";
                @endif
            }

            // Update the countdown every second
            const countdownTimer = setInterval(function() {
                // Fonction pour mettre à jour un élément avec animation
                function updateElementWithAnimation(element, value) {
                    // Vérifie si la valeur a changé
                    if (element.textContent !== value) {
                        // Ajoute la classe d'animation
                        element.classList.add('updating');

                        // Met à jour le contenu et la variable CSS
                        element.textContent = value;
                        element.style.setProperty('--value', parseInt(value, 10));

                        // Supprime la classe d'animation après 400ms
                        setTimeout(() => {
                            element.classList.remove('updating');
                        }, 400);
                    }
                }

                // Get current date and time
                const now = new Date().getTime();

                // Find the time remaining between now and the event date
                const timeRemaining = eventDate - now;

                // Calculate days, hours, minutes and seconds
                const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                // Display the result in the elements with corresponding ids
                const daysElement = document.getElementById('countdown-days');
                const hoursElement = document.getElementById('countdown-hours');
                const minutesElement = document.getElementById('countdown-minutes');
                const secondsElement = document.getElementById('countdown-seconds');

                // Update with animation
                updateElementWithAnimation(daysElement, days.toString().padStart(2, '0'));
                updateElementWithAnimation(hoursElement, hours.toString().padStart(2, '0'));
                updateElementWithAnimation(minutesElement, minutes.toString().padStart(2, '0'));
                updateElementWithAnimation(secondsElement, seconds.toString().padStart(2, '0'));

                // If the countdown is over, display a message
                if (timeRemaining < 0) {
                    clearInterval(countdownTimer);
                    @if ($nearestEvent)
                        // Check if the event has ended
                        const eventEndDate = new Date('{{ $nearestEvent->end_date->format('Y-m-d') }} 23:59:59').getTime();
                        if (now > eventEndDate) {
                            document.getElementById('countdown-timer').innerHTML =
                                "<div class='alert alert-info text-center'><span class='text-xl font-bold'>{{ __('The event has ended!') }}</span></div>";
                        } else {
                            document.getElementById('countdown-timer').innerHTML =
                                "<div class='alert alert-success text-center'><span class='text-xl font-bold'>{{ __('The event is ongoing!') }}</span></div>";
                        }
                    @else
                        document.getElementById('countdown-timer').innerHTML =
                            "<div class='alert alert-success text-center'><span class='text-xl font-bold'>{{ __('The event is ongoing!') }}</span></div>";
                    @endif
                }
            }, 1000);
        </script>
    </article>
