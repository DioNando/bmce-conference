@props(['usersByCountry', 'totalUsers'])

{{-- Carte des utilisateurs par pays --}}
<section class="flex flex-col gap-6 h-full flex-1">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{-- <x-heroicon-s-globe-europe-africa class="size-6" /> --}}
                {{ __('Users by Country') }}
            </h3>
            <p class="text-sm text-base-content/70 mt-1">
                {{ __('Geographic distribution of users across the platform') }}
            </p>
        </div>
        <div class="flex gap-2">
            <select id="map-view-selector" class="select select-sm select-bordered">
                <option value="all">{{ __('All Countries') }}</option>
                <option value="top10">{{ __('Top 10 Countries') }}</option>
            </select>
        </div>
    </div>

    <div class="card card-border bg-base-100 shadow-lg overflow-hidden">
        <div class="card-body">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Carte SVG -->
                <div class="col-span-2 h-[400px] relative bg-base-200 card p-4">
                    <div
                        class="absolute top-2 right-2 z-10 alert alert-soft alert-info p-2 mb-4 flex w-fit justify-end items-center gap-2">
                        <x-heroicon-s-information-circle class="size-5" />
                        <span
                            class="text-xs">{{ __('Click on a country or select from the list to see detailed information.') }}</span>
                    </div>
                    <div id="world-map" class="w-full h-full flex items-center justify-center"></div>
                </div>

                <!-- Liste des pays -->
                <div class="card shadow-md self-start overflow-hidden bg-base-100">
                    <div class="p-3 border-b border-base-200">
                        <div class="flex items-center justify-between gap-2">
                            <input type="text" id="country-search" class="input input-sm input-bordered w-full"
                                placeholder="{{ __('Search countries...') }}">
                            <button class="btn btn-sm btn-ghost btn-square" id="clear-search"
                                title="{{ __('Clear search') }}">
                                <x-heroicon-o-x-mark class="size-4" />
                            </button>
                        </div>
                    </div>
                    <div class="overflow-y-auto max-h-[350px]">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th class="text-left">{{ __('Country') }}</th>
                                    <th class="text-center">{{ __('Code') }}</th>
                                    <th class="text-right">{{ __('Users') }}</th>
                                </tr>
                            </thead>
                            <tbody id="countries-tbody">
                                @foreach ($usersByCountry as $country)
                                    <tr class="hover hover:bg-primary hover:text-white cursor-pointer country-row"
                                        data-country-code="{{ $country->code }}"
                                        data-country-name="{{ strtolower($country->country) }}">
                                        <td class="font-medium">{{ $country->country }}</td>
                                        <td class="text-center font-mono">{{ $country->code }}</td>
                                        <td class="text-right">
                                            <span class="badge badge-success rounded-full">{{ $country->total }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="no-results" class="hidden p-4 text-center text-base-content/70">
                        <x-heroicon-o-magnifying-glass class="mx-auto h-8 w-8 text-base-content/30 mb-2" />
                        {{ __('No countries match your search') }}
                    </div>
                </div>
            </div>
            <!-- Zone d'information du pays sélectionné -->
            <div class="mt-4 card min-h-[80px]" id="country-details-container">
                <livewire:country-users wire:key="country-users" />
            </div>
        </div>
    </div>
</section>

<!-- Ressources pour l'affichage de la carte -->
@push('scripts')
    <script src="https://unpkg.com/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"></script>
    <script src="https://unpkg.com/jsvectormap@1.5.3/dist/maps/world.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration de la carte
            const usersByCountry = @json($usersByCountry);
            const totalUsers = {{ $totalUsers ?? 'null' }};
            const mapData = {};

            usersByCountry.forEach(function(item) {
                mapData[item.code.toUpperCase()] = item.total;
            });

            // Initialisation de la carte
            const map = new jsVectorMap({
                selector: '#world-map',
                map: 'world',
                zoomOnScroll: true,
                zoomButtons: true,
                markersSelectable: true,
                regionsSelectable: true,
                regionsSelectableOne: true,
                markers: [],
                markerStyle: {
                    initial: {
                        fill: 'var(--color-primary)'
                    },
                    selected: {
                        fill: 'var(--color-primary-focus)'
                    }
                },
                labels: {
                    markers: {
                        render: marker => marker.name
                    }
                },
                // selectedRegions: ['EG', 'US'],
                series: {
                    regions: [{
                        values: mapData,
                        scale: ['#e6f7e6', '#c2e6c2', '#99d699', '#66c266', 'var(--color-success)'],
                        normalizeFunction: 'polynomial',
                    }]
                },
                regionStyle: {
                    initial: {
                        fill: 'var(--color-base-200)',
                        stroke: 'var(--color-base-content)',
                        strokeWidth: 1,
                    },
                    hover: {
                        fill: 'var(--color-primary-focus)',
                        cursor: 'pointer'
                    },
                    selected: {
                        fill: 'var(--color-primary)'
                    },
                    selectedHover: {
                        fill: 'var(--color-primary)'
                    }
                },
                onRegionTooltipShow(event, tooltip, code) {
                    tooltip.css({
                        background: 'none',
                        border: 'none',
                        boxShadow: 'none'
                    });
                    const countryName = tooltip.text();
                    const userCount = mapData[code] || 0;

                    // Construire un contenu informatif pour l'infobulle
                    let tooltipContent = `<div class="card bg-base-100 p-4 shadow-md">
                    <h5 class="font-bold text-base-content">${countryName}</h5>`;
                    if (userCount > 0) {
                        // Calculer le pourcentage par rapport au nombre total d'utilisateurs
                        const totalUsersCount = totalUsers || Object.values(mapData).reduce((sum, count) =>
                            sum + count, 0);
                        const percentage = totalUsersCount > 0 ? ((userCount / totalUsersCount) * 100)
                            .toFixed(1) : 0;

                        tooltipContent += `
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs font-medium">{{ __('Users') }}:</span>
                        <span class="badge badge-success badge-sm rounded-full ml-2">${userCount}</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs font-medium">{{ __('Percentage') }}:</span>
                        <span class="text-xs font-bold">${percentage}%</span>
                    </div>
                    <div class="w-full bg-base-300 rounded-full h-1.5 mt-1.5">
                        <div class="bg-primary h-1.5 rounded-full" style="width: ${percentage}%"></div>
                    </div>`;
                    } else {
                        tooltipContent += `
                    <p class="text-xs text-base-content/70 mt-1">{{ __('No users from this country') }}</p>`;
                    }

                    tooltipContent += `</div>`;
                    tooltip.text(tooltipContent, true);
                },
                onRegionClick: function(event, code) {
                    // Déclencher l'événement Livewire pour mettre à jour le composant
                    selectCountry(code);
                }
            });

            // Fonction pour gérer la sélection de pays
            function selectCountry(code) {
                // Mettre à jour la carte
                // map.setSelectedRegions(code);

                // Mettre à jour la table
                document.querySelectorAll('.country-row').forEach(row => {
                    if (row.getAttribute('data-country-code') === code) {
                        row.classList.add('bg-primary', 'text-white');
                    } else {
                        row.classList.remove('bg-primary', 'text-white');
                    }
                });

                // Afficher l'indicateur de chargement avec un événement Livewire
                // Au lieu de modifier l'HTML directement, on va utiliser un événement
                Livewire.dispatch('showLoading');

                // Déclencher l'événement Livewire
                Livewire.dispatch('countrySelected', {
                    code: code
                });
            }

            // Rendre les lignes de la table cliquables
            document.querySelectorAll('.country-row').forEach(row => {
                row.addEventListener('click', function() {
                    const countryCode = this.getAttribute('data-country-code');
                    selectCountry(countryCode);
                });
            });

            // Fonctionnalité pour afficher les top pays
            const viewSelector = document.getElementById('map-view-selector');
            viewSelector.addEventListener('change', function() {
                filterTopCountries(this.value);
            });

            function filterTopCountries(viewMode) {
                const rows = tbody.querySelectorAll('tr.country-row');

                // Réinitialiser d'abord la recherche
                searchInput.value = '';
                noResults.classList.add('hidden');

                if (viewMode === 'top10') {
                    // Récupérer toutes les lignes dans un tableau pour pouvoir les trier
                    const rowsArray = Array.from(rows);

                    // Trier les pays par nombre d'utilisateurs (en ordre décroissant)
                    rowsArray.sort((a, b) => {
                        const usersA = parseInt(a.querySelector('span.badge').textContent);
                        const usersB = parseInt(b.querySelector('span.badge').textContent);
                        return usersB - usersA;
                    });

                    // Afficher seulement les 10 premiers pays
                    rowsArray.forEach((row, index) => {
                        if (index < 10) {
                            row.classList.remove('hidden');
                        } else {
                            row.classList.add('hidden');
                        }
                    });

                    // Mettre à jour le message si aucun pays n'est affiché
                    if (rowsArray.length === 0) {
                        noResults.classList.remove('hidden');
                    }
                } else {
                    // Afficher tous les pays
                    rows.forEach(row => {
                        row.classList.remove('hidden');
                    });
                }
            }

            // Fonctionnalité de recherche pour les pays
            const searchInput = document.getElementById('country-search');
            const clearButton = document.getElementById('clear-search');
            const tbody = document.getElementById('countries-tbody');
            const noResults = document.getElementById('no-results');

            searchInput.addEventListener('input', filterCountries);
            clearButton.addEventListener('click', clearSearch);

            function filterCountries() {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = tbody.querySelectorAll('tr.country-row');
                let matchFound = false;

                rows.forEach(row => {
                    const countryName = row.getAttribute('data-country-name');
                    const countryCode = row.getAttribute('data-country-code').toLowerCase();

                    if (countryName.includes(searchTerm) || countryCode.includes(searchTerm)) {
                        row.classList.remove('hidden');
                        matchFound = true;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                // Afficher ou masquer le message "Aucun résultat"
                if (matchFound) {
                    noResults.classList.add('hidden');
                } else {
                    noResults.classList.remove('hidden');
                }
            }

            function clearSearch() {
                searchInput.value = '';
                filterCountries();
                searchInput.focus();
            }
        });
    </script>
@endpush
