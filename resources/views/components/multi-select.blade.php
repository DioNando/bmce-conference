@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => __('-- Select --'),
    'options' => [],
    'selected' => [],
    'required' => false,
    'isMultiple' => true,
    'allowSearch' => true,
    'nameKey' => 'name',
    'valueKey' => 'id',
    'organizationKey' => null,
    'emailKey' => null,
])

@php
    $id = $id ?? $name;
    $uniqueId = "multi-select-" . uniqid();
    $selectedJson = json_encode($selected);
    $optionsJson = json_encode($options);
    $isMultipleValue = $isMultiple ? 'true' : 'false';
@endphp

<div id="{{ $uniqueId }}" class="relative multi-select-container">
    @if($label)
        <label class="label">
            <span class="label-text">{{ $label }}{{ $required ? '*' : '' }}</span>
        </label>
    @endif

    <div class="mt-1">
        <!-- Selected options display -->
        <div class="select w-full cursor-pointer flex items-center justify-between multi-select-display">
            <div class="flex-1 truncate multi-select-text">{{ $placeholder }}</div>
        </div>

        <!-- Hidden actual checkboxes for form submission -->
        <div class="multi-select-checkboxes">
            @foreach($options as $option)
                <input type="checkbox" name="{{ $isMultiple ? $name.'[]' : $name }}" value="{{ $option->{$valueKey} }}"
                    class="hidden multi-select-checkbox"
                    {{ in_array($option->{$valueKey}, $selected) ? 'checked' : '' }}>
            @endforeach
        </div>

        <!-- Dropdown -->
        <div class="dropdown-content card card-compact shadow bg-base-100 w-full max-h-60 overflow-y-auto absolute z-50 mt-1 multi-select-dropdown"
             style="display: none;">

            <!-- Search box -->
            @if($allowSearch)
            <div class="card-body p-2 border-b border-base-200">
                <input
                    type="search"
                    class="input input-sm input-bordered w-full multi-select-search"
                    placeholder="{{ __('Search...') }}">
            </div>
            @endif

            <!-- No results message -->
            <div class="p-4 text-sm text-base-content/70 multi-select-no-results" style="display: none;">
                {{ __('No results found') }}
            </div>

            <!-- Options list -->
            <div class="multi-select-options">
                @foreach($options as $option)
                    <div data-value="{{ $option->{$valueKey} }}"
                         class="px-4 py-2 flex items-center cursor-pointer hover:bg-base-200 group multi-select-option {{ in_array($option->{$valueKey}, $selected) ? 'bg-primary/10' : '' }}">
                        <div class="flex items-center justify-center">
                            <div class="w-5 h-5 transition-all duration-200 rounded-full check-icon bg-primary/10"
                                 style="{{ !in_array($option->{$valueKey}, $selected) ? 'display: none;' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="w-5 h-5 rounded-full border border-base-300 uncheck-icon"
                                 style="{{ in_array($option->{$valueKey}, $selected) ? 'display: none;' : '' }}"></div>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium">{{ ($option->first_name ? $option->first_name.' ' : '').$option->{$nameKey} }}</div>
                            @if(isset($option->{$organizationKey}) || (isset($option->organization) && isset($option->organization->name)))
                                <div class="text-sm text-base-content/70">
                                    @if($organizationKey && isset($option->{$organizationKey}))
                                        {{ $option->{$organizationKey} }}
                                    @elseif(isset($option->organization) && isset($option->organization->name))
                                        {{ $option->organization->name }}
                                    @endif
                                </div>
                            @endif
                            @if(isset($option->{$emailKey ?? 'email'}))
                                <div class="text-xs text-base-content/50 mt-1">{{ $option->{$emailKey ?? 'email'} }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all multi-select components
    initMultiSelect('{{ $uniqueId }}', {
        name: '{{ $name }}',
        isMultiple: {{ $isMultipleValue }},
        selected: {!! $selectedJson !!},
        options: {!! $optionsJson !!},
        valueKey: '{{ $valueKey }}',
        nameKey: '{{ $nameKey }}',
        placeholder: '{{ $placeholder }}',
    });
});

function initMultiSelect(id, config) {
    const container = document.getElementById(id);
    if (!container) return;

    // Cache DOM elements
    const display = container.querySelector('.multi-select-display');
    const text = container.querySelector('.multi-select-text');
    const dropdown = container.querySelector('.multi-select-dropdown');
    const search = container.querySelector('.multi-select-search');
    const noResults = container.querySelector('.multi-select-no-results');
    const options = container.querySelectorAll('.multi-select-option');
    const checkboxes = container.querySelectorAll('.multi-select-checkbox');

    // State management
    let state = {
        open: false,
        searchValue: '',
        selectedOptions: config.selected || []
    };

    // Update the display text based on selected options
    function updateDisplayText() {
        if (state.selectedOptions.length === 0) {
            text.textContent = config.placeholder;
            display.classList.remove('select-primary');
        } else if (state.selectedOptions.length === 1) {
            const option = config.options.find(opt => opt[config.valueKey] === state.selectedOptions[0]);
            if (option) {
                const firstName = option.first_name ? option.first_name + ' ' : '';
                text.textContent = firstName + option[config.nameKey];
            } else {
                text.textContent = config.placeholder;
            }
            display.classList.add('select-primary');
        } else {
            text.textContent = `${state.selectedOptions.length} {{ __('items selected') }}`;
            display.classList.add('select-primary');
        }
    }

    // Update checkboxes based on selection
    function updateCheckboxes() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = state.selectedOptions.includes(parseInt(checkbox.value));
        });

        // Dispatch an event for other components to listen to
        const event = new CustomEvent('multi-select-change', {
            detail: {
                name: config.name,
                selectedValues: state.selectedOptions
            }
        });
        window.dispatchEvent(event);
    }

    // Toggle dropdown visibility
    function toggleDropdown() {
        state.open = !state.open;
        dropdown.style.display = state.open ? 'block' : 'none';

        if (state.open && search) {
            setTimeout(() => search.focus(), 0);
        }

        // Add click outside listener when opening
        if (state.open) {
            document.addEventListener('click', handleClickOutside);
        } else {
            document.removeEventListener('click', handleClickOutside);
        }
    }

    // Close dropdown when clicking outside
    function handleClickOutside(e) {
        if (!container.contains(e.target)) {
            state.open = false;
            dropdown.style.display = 'none';
            document.removeEventListener('click', handleClickOutside);
        }
    }

    // Toggle selection of an option
    function toggleOption(optionValue) {
        const value = parseInt(optionValue);
        const index = state.selectedOptions.indexOf(value);

        // Vérifier si on est dans un cas one-on-one (en vérifiant le radio button)
        const isOneOnOne = document.getElementById('is_one_on_one_1')?.checked;

        if (config.isMultiple) {
            if (index === -1) {
                // Si one-on-one est activé et qu'on essaie d'ajouter une option alors qu'il y en a déjà
                if (isOneOnOne && state.selectedOptions.length >= 1 && config.name === 'investor_ids') {
                    // Remplacer l'option sélectionnée par la nouvelle
                    state.selectedOptions = [value];

                    // Notification pour informer l'utilisateur (avec style daisyUI)
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: {
                            type: 'warning',
                            message: "{{ __('A One-on-One meeting can only have one investor.') }}"
                        }
                    }));
                } else {
                    state.selectedOptions.push(value);
                }
            } else {
                state.selectedOptions.splice(index, 1);
            }
        } else {
            state.selectedOptions = [value];
            state.open = false;
            dropdown.style.display = 'none';
        }

        updateDisplayText();
        updateCheckboxes();
        updateOptionDisplay();
    }

    // Update option display based on selection
    function updateOptionDisplay() {
        options.forEach(option => {
            const value = parseInt(option.dataset.value);
            const isSelected = state.selectedOptions.includes(value);

            if (isSelected) {
                option.classList.add('bg-primary/10');
            } else {
                option.classList.remove('bg-primary/10');
            }

            const checkIcon = option.querySelector('.check-icon');
            const uncheckIcon = option.querySelector('.uncheck-icon');

            if (checkIcon) checkIcon.style.display = isSelected ? 'block' : 'none';
            if (uncheckIcon) uncheckIcon.style.display = isSelected ? 'none' : 'block';
        });
    }

    // Filter options based on search term
    function filterOptions() {
        if (!search) return;

        const searchTerm = search.value.trim().toLowerCase();
        let visibleCount = 0;

        options.forEach(option => {
            const optionValue = parseInt(option.dataset.value);
            const optionData = config.options.find(opt => opt[config.valueKey] === optionValue);

            if (!optionData) {
                option.style.display = 'none';
                return;
            }

            const name = optionData[config.nameKey]?.toLowerCase() || '';
            const firstName = optionData.first_name?.toLowerCase() || '';
            const fullName = (firstName + ' ' + name).toLowerCase();

            let org = '';
            try {
                org = optionData.organization?.name?.toLowerCase() || '';
            } catch (e) {}

            let email = '';
            try {
                email = optionData.email?.toLowerCase() || '';
            } catch (e) {}

            const visible = !searchTerm ||
                name.includes(searchTerm) ||
                firstName.includes(searchTerm) ||
                fullName.includes(searchTerm) ||
                org.includes(searchTerm) ||
                email.includes(searchTerm);

            option.style.display = visible ? 'flex' : 'none';
            if (visible) visibleCount++;
        });

        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // Event listeners
    display.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleDropdown();
    });

    if (search) {
        search.addEventListener('input', filterOptions);
        search.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                e.preventDefault();
                state.open = false;
                dropdown.style.display = 'none';
            }
        });
    }

    options.forEach(option => {
        option.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleOption(option.dataset.value);
        });
    });

    // Écouter les changements du radio button one-on-one
    if (config.name === 'investor_ids') {
        const oneOnOneRadio = document.getElementById('is_one_on_one_1');
        if (oneOnOneRadio) {
            oneOnOneRadio.addEventListener('change', function() {
                // Si on passe en mode one-on-one et qu'il y a plusieurs investisseurs sélectionnés
                if (this.checked && state.selectedOptions.length > 1) {
                    // Garder uniquement le dernier investisseur sélectionné
                    const lastSelectedId = state.selectedOptions[state.selectedOptions.length - 1];
                    state.selectedOptions = [lastSelectedId];

                    // Notification pour informer l'utilisateur
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: {
                            type: 'warning',
                            message: "{{ __('A One-on-One meeting can only have one investor.') }}"
                        }
                    }));

                    // Mettre à jour l'affichage
                    updateDisplayText();
                    updateCheckboxes();
                    updateOptionDisplay();
                }
            });
        }
    }

    // Listen for special events
    window.addEventListener('multi-select-enforce-limit', function(e) {
        // Vérifiez si l'événement est destiné à cette instance de multi-select
        if (e.detail && e.detail.name === config.name) {
            // Si des valeurs spécifiques sont fournies, appliquez-les
            if (e.detail.selectedValues) {
                state.selectedOptions = e.detail.selectedValues;
                updateDisplayText();
                updateCheckboxes();
                updateOptionDisplay();
            }
        }
    });

    // Initialize display
    updateDisplayText();
    updateCheckboxes();
    updateOptionDisplay();
}
</script>
