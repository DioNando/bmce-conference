<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('investor.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li>{{ __('Issuers Directory') }}</li>
        </ul>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">
            {{ __('Issuers Directory') }}
        </h1>
    </div>

    <section class="space-y-6">

        <div class="card card-bordered bg-base-200 shadow-lg">
            <div class="card-body" x-data="{ showFilters: false }">
                <!-- Search & Filter (To implement later) -->
                <div class="flex justify-between items-center mb-4">
                    <div class="form-control w-full max-w-xs">
                        <label class="input">
                            <x-heroicon-s-magnifying-glass class="size-4 opacity-50" />
                            <input type="search" class="grow" name="search"
                                placeholder="{{ __('Search issuers...') }}" value="{{ request('search') }}"
                                form="issuers-filter-form">
                        </label>
                    </div>
                    <button type="button" class="btn btn-primary" @click="showFilters = !showFilters">
                        <x-heroicon-s-funnel class="size-4" />
                        <span class="hidden lg:block">{{ __('Filters') }}</span>
                        @if (request('search') ||
                                (request('org_type') && request('org_type') != 'all') ||
                                (request('country') && request('country') != 'all') ||
                                (request('origin') && request('origin') != 'all'))
                            <div class="badge badge-sm badge-white rounded-full">{{ __('active') }}</div>
                        @endif
                    </button>
                </div>

                <!-- Expanded Filter Form -->
                <form id="issuers-filter-form" action="{{ route('investor.issuers.index') }}" method="GET"
                    x-show="showFilters" x-cloak
                    class="bg-base-100 shadow-md p-4 rounded-box mb-4 transition-all duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Organization Type') }}</legend>
                                <select id="org_type" name="org_type" class="select select-bordered w-full">
                                    <option value="all" {{ request('org_type') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Types') }}</option>
                                    @foreach (\App\Enums\OrganizationType::cases() as $type)
                                        <option value="{{ $type->value }}"
                                            {{ request('org_type') == $type->value ? 'selected' : '' }}>
                                            {{ $type->englishLabel() }}
                                        </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Country') }}</legend>
                                <select id="country" name="country" class="select select-bordered w-full">
                                    <option value="all" {{ request('country') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Countries') }}</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            {{ request('country') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Origin') }}</legend>
                                <select id="origin" name="origin" class="select select-bordered w-full">
                                    <option value="all" {{ request('origin') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Origins') }}</option>
                                    <option value="{{ \App\Enums\Origin::NATIONAL->value }}"
                                        {{ request('origin') == \App\Enums\Origin::NATIONAL->value ? 'selected' : '' }}>
                                        {{ __('National') }}
                                    </option>
                                    <option value="{{ \App\Enums\Origin::FOREIGN->value }}"
                                        {{ request('origin') == \App\Enums\Origin::FOREIGN->value ? 'selected' : '' }}>
                                        {{ __('Foreign') }}
                                    </option>
                                </select>
                            </fieldset>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4 justify-end">
                        @if (request('search') ||
                                (request('org_type') && request('org_type') != 'all') ||
                                (request('country') && request('country') != 'all') ||
                                (request('origin') && request('origin') != 'all'))
                            <a href="{{ route('investor.issuers.index') }}" class="btn btn-ghost">
                                {{ __('Reset Filters') }}
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <span>{{ __('Apply Filters') }}</span>
                        </button>
                    </div>
                </form>
                <div>
                    @if ($issuers->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                            @foreach ($issuers as $issuer)
                                <div
                                    class="card card-bordered bg-base-100 shadow-lg hover:border-primary transition-colors overflow-hidden">
                                    <div
                                        class="bg-primary/10 text-primary px-5 py-3 text-sm flex justify-between items-center">
                                        <h4 class="font-medium uppercase">
                                            {{ $issuer->organization->name ?? 'No Organization' }}</h4>
                                        <div class="flex-shrink-0">
                                            @if ($issuer->profile_photo_path)
                                                <img class="size-8 rounded-full object-cover"
                                                    src="{{ $issuer->profile_photo_url }}" alt="{{ $issuer->name }}">
                                            @else
                                                <div
                                                    class="size-8 rounded-full bg-primary flex items-center justify-center text-white text-base font-semibold">
                                                    {{ substr($issuer->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body p-5">
                                        <div class="mb-3">
                                            <h3 class="font-semibold text-lg mb-1 text-base-content">
                                                {{ $issuer->first_name }} {{ $issuer->name }}
                                            </h3>
                                            <p class="text-sm text-primary font-medium">
                                                {{ $issuer->position ?? __('Position not specified') }}
                                            </p>
                                        </div>

                                        <div class="space-y-2 mb-4">
                                            @if ($issuer->organization && $issuer->organization->organization_type)
                                                <div class="flex items-center gap-2 text-xs text-base-content/80">
                                                    <x-heroicon-s-building-office class="size-4 text-primary/70" />
                                                    <span>{{ $issuer->organization->organization_type->englishLabel() }}</span>
                                                </div>
                                            @endif

                                            @if ($issuer->organization && $issuer->organization->country)
                                                <div class="flex items-center gap-2 text-xs text-base-content/80">
                                                    <x-heroicon-s-globe-europe-africa class="size-4 text-primary/70" />
                                                    <span>{{ $issuer->organization->country->name_en ?? __('Not specified') }}</span>
                                                    @if ($issuer->organization->origin)
                                                        <span class="badge badge-xs ml-1">
                                                            {{ __($issuer->organization->origin->value) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($issuer->email)
                                                <div class="flex items-center gap-2 text-xs text-base-content/80">
                                                    <x-heroicon-s-envelope class="size-4 text-primary/70" />
                                                    <span>{{ $issuer->email }}</span>
                                                </div>
                                            @endif

                                            @if ($issuer->phone)
                                                <div class="flex items-center gap-2 text-xs text-base-content/80">
                                                    <x-heroicon-s-phone class="size-4 text-primary/70" />
                                                    <span>{{ $issuer->phone }}</span>
                                                </div>
                                            @endif

                                            @if ($issuer->organization && $issuer->organization->fiche_bkgr)
                                                <div class="flex items-center gap-2 text-xs text-primary">
                                                    <x-heroicon-s-document-text class="size-4" />
                                                    <span class="font-medium">{{ __('BKGR Available') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="card-actions justify-between items-center mt-auto">
                                            <a href="{{ route('investor.issuers.show', $issuer->id) }}"
                                                class="text-primary hover:text-primary/70 text-sm font-medium flex items-center gap-1">
                                                <x-heroicon-s-user class="size-4" />
                                                {{-- {{ __('View Profile') }} --}}
                                            </a>
                                            @php
                                                $existingMeeting = auth()
                                                    ->user()
                                                    ->investorMeetings()
                                                    ->where('issuer_id', $issuer->id)
                                                    ->first();
                                            @endphp

                                            @if ($existingMeeting)
                                                <a href="{{ route('investor.meetings.show', $existingMeeting->id) }}"
                                                    class="btn btn-success btn-sm rounded-full">
                                                    <x-heroicon-s-calendar class="size-4" />
                                                    <span>{{ __('View Meeting') }}</span>
                                                </a>
                                            @else
                                                <a href="{{ route('investor.meeting.request', $issuer->id) }}"
                                                    class="btn btn-primary btn-sm rounded-full">
                                                    <x-heroicon-s-calendar class="size-4" />
                                                    <span>{{ __('Request Meeting') }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- @if ($issuers->hasPages())
                        <div class="mt-6">
                            {{ $issuers->links() }}
                        </div>
                    @endif --}}
                    @else
                        <div class="text-center py-8">
                            <x-heroicon-s-exclamation-triangle class="size-16 mx-auto text-base-content/30" />
                            <h3 class="mt-2 text-sm font-medium text-base-content">{{ __('No issuers available') }}
                            </h3>
                            <p class="mt-1 text-sm text-base-content/70">
                                {{ __('No issuers are currently available in the system.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
