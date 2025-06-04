@php
    use App\Enums\UserRole;
    use App\Enums\OrganizationType;
    use App\Enums\Origin;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li>{{ __('Organizations') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Organizations List') }}
            </h3>
            <div class="flex gap-3">
                <a href="{{ route('admin.organizations.import.form') }}" class="btn btn-outline">
                    <x-heroicon-s-arrow-up-tray class="size-4" />
                    <div class="hidden lg:block">{{ __('Import') }}</div>
                </a>
                <a href="{{ route('admin.organizations.export', request()->query()) }}" class="btn btn-outline">
                    <x-heroicon-s-arrow-down-tray class="size-4" />

                    <span class="hidden lg:block">{{ __('Export') }}</span>
                </a>
                <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary">
                    <x-heroicon-s-plus class="size-4" />
                    <div class="hidden lg:block">
                        {{ __('Add Organization') }}
                    </div>
                </a>
            </div>
        </div>
    </x-slot>
    <section class="space-y-6">
        @if (session('success'))
            <div role="alert" class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="alert alert-error">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="card card-bordered bg-base-200 shadow-lg" x-data="{ showFilters: false }">
            <div class="card-body">
                <!-- Search and Filter Controls -->
                <div class="flex justify-between items-center mb-4 gap-4">
                    <div class="form-control w-full max-w-xs">
                        <label class="input">
                            <x-heroicon-s-magnifying-glass class="size-4 opacity-50" />
                            <input type="search" class="grow" name="search" placeholder="Search organizations..."
                                value="{{ request('search') }}" form="organizations-filter-form">
                        </label>
                    </div>
                    <button type="button" class="btn btn-primary" @click="showFilters = !showFilters">
                        <x-heroicon-s-funnel class="size-4" />
                        <span class="hidden lg:block">{{ __('Filters') }}</span>
                        @if (request('search') ||
                                (request('profile') && request('profile') != 'all') ||
                                (request('origin') && request('origin') != 'all') ||
                                (request('type') && request('type') != 'all') ||
                                (request('country') && request('country') != 'all'))
                            <div class="badge badge-sm badge-white rounded-full">{{ __('active') }}</div>
                        @endif
                    </button>
                </div>

                <!-- Expanded Filter Form -->
                <form id="organizations-filter-form" action="{{ route('admin.organizations.index') }}" method="GET"
                    x-show="showFilters" x-cloak
                    class="bg-base-100 shadow-md p-4 rounded-box mb-4 transition-all duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Profile') }}</legend>
                                <select id="profile" name="profile" class="select select-bordered w-full">
                                    <option value="all" {{ request('profile') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Profiles') }}</option>
                                    <option value="{{ UserRole::ISSUER->value }}"
                                        {{ request('profile') == UserRole::ISSUER->value ? 'selected' : '' }}>
                                        {{ UserRole::ISSUER->label() }}
                                    </option>
                                    <option value="{{ UserRole::INVESTOR->value }}"
                                        {{ request('profile') == UserRole::INVESTOR->value ? 'selected' : '' }}>
                                        {{ UserRole::INVESTOR->label() }}
                                    </option>
                                </select>
                            </fieldset>
                        </div>
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Origin') }}</legend>
                                <select id="origin" name="origin" class="select select-bordered w-full">
                                    <option value="all" {{ request('origin') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Origins') }}</option>
                                    <option value="{{ Origin::NATIONAL->value }}"
                                        {{ request('origin') == Origin::NATIONAL->value ? 'selected' : '' }}>
                                        {{ Origin::NATIONAL->label() }}
                                    </option>
                                    <option value="{{ Origin::FOREIGN->value }}"
                                        {{ request('origin') == Origin::FOREIGN->value ? 'selected' : '' }}>
                                        {{ Origin::FOREIGN->label() }}
                                    </option>
                                </select>
                            </fieldset>
                        </div>
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Organization Type') }}</legend>
                                <select id="type" name="type" class="select select-bordered w-full">
                                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Types') }}</option>
                                    @foreach (OrganizationType::cases() as $type)
                                        <option value="{{ $type->value }}"
                                            {{ request('type') == $type->value ? 'selected' : '' }}>
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
                    </div>

                    <div class="flex gap-3 mt-4 justify-end">
                        @if (request('search') ||
                                request('profile') != 'all' ||
                                request('origin') != 'all' ||
                                request('type') != 'all' ||
                                request('country') != 'all')
                            <a href="{{ route('admin.organizations.index') }}" class="btn btn-ghost">
                                {{ __('Reset Filters') }}
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            {{ __('Apply Filters') }}
                        </button>
                    </div>

                    <!-- Hidden fields to preserve sort values when filtering -->
                    <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'name' }}">
                    <input type="hidden" name="sort_order" value="{{ $sortOrder ?? 'asc' }}">
                </form>

                @php
                    $headers = [
                        ['content' => 'Name', 'align' => 'text-left', 'sortable' => true, 'sortField' => 'name'],
                        ['content' => 'Origin', 'align' => 'text-center'],
                        ['content' => 'Profile', 'align' => 'text-center'],
                        ['content' => 'Type', 'align' => 'text-left'],
                        [
                            'content' => 'Country',
                            'align' => 'text-left',
                            'sortable' => true,
                            'sortField' => 'country_id',
                        ],
                        ['content' => 'BKGR', 'align' => 'text-center'],
                        ['content' => 'Actions', 'align' => 'text-right'],
                    ];
                    $empty = 'No organizations found';

                    // Get current sort parameters
                    $sortBy = request('sort_by', 'name');
                    $sortOrder = request('sort_order', 'asc');
                @endphp

                <div class="card bg-base-100 shadow-sm overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th><span class="sr-only">logo</span></th>
                                @foreach ($headers as $header)
                                    <x-table.sortable-header label="{{ $header['content'] }}"
                                        field="{{ $header['sortField'] ?? '' }}"
                                        sortable="{{ $header['sortable'] ?? false }}"
                                        align="{{ str_replace('text-', '', $header['align']) }}" />
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($organizations as $organization)
                                <tr>
                                    <td>
                                        <div class="w-0">
                                            @if ($organization->logo)
                                                <div class="h-10 w-10 rounded-full overflow-hidden">
                                                    <img class="h-full w-full object-cover"
                                                        src="{{ Storage::url($organization->logo) }}"
                                                        alt="{{ $organization->name }}">
                                                </div>
                                            @elseif ($organization->profil == App\Enums\UserRole::ISSUER)
                                                <div
                                                    class="h-10 w-10 rounded-full bg-warning flex items-center justify-center text-white text-lg font-mono font-semibold">
                                                    {{ substr($organization->name, 0, 1) }}
                                                </div>
                                            @elseif ($organization->profil == App\Enums\UserRole::INVESTOR)
                                                <div
                                                    class="h-10 w-10 rounded-full bg-primary flex items-center justify-center text-white text-lg font-mono font-semibold">
                                                    {{ substr($organization->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="font-medium">
                                        <span>{{ $organization->name }}</span>
                                    </td>
                                    <td class="text-center text-base-content/70">{{ $organization->origin->label() }}
                                    </td>
                                    <td class="text-center">
                                        @if ($organization->profil == App\Enums\UserRole::ISSUER)
                                            <span
                                                class="badge badge-warning">{{ $organization->profil->label() }}</span>
                                        @elseif ($organization->profil == App\Enums\UserRole::INVESTOR)
                                            <span
                                                class="badge badge-primary">{{ $organization->profil->label() }}</span>
                                        @else
                                            <span class="badge">{{ $organization->profil->label() }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($organization->organization_type == \App\Enums\OrganizationType::AUTRE)
                                            {{ $organization->organization_type_other }} <span
                                                class="badge badge-sm badge-ghost rounded-full">
                                                {{ __('other') }}
                                            </span>
                                        @else
                                            {{ $organization->organization_type->englishLabel() }}
                                        @endif
                                    </td>
                                    <td>{{ $organization->country->name_en }}</td>
                                    {{-- FICHE BKGR --}}
                                    <td class="text-center">
                                        @if ($organization->fiche_bkgr)
                                            <a href="{{ Storage::url($organization->fiche_bkgr) }}" target="_blank"
                                                class="btn btn-sm btn-square btn-primary">
                                                <x-heroicon-s-eye class="size-4" />
                                            </a>
                                        @else
                                            <span class="text-base-content/30">-</span>
                                        @endif
                                    </td>
                                    {{-- FICHE BKGR --}}
                                    <td class="text-right">
                                        <div class="flex gap-2 items-center justify-end">
                                            <a href="{{ route('admin.organizations.edit', $organization) }}"
                                                class="btn btn-sm btn-success">{{ __('Edit') }}</a>
                                            <button type="button" class="btn btn-sm btn-square btn-error"
                                                @click="$dispatch('open-modal', 'delete-organization-{{ $organization->id }}')"><x-heroicon-s-trash
                                                    class="size-4" /></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($headers) }}"
                                        class="text-center py-5 text-base-content/70">
                                        {{ $empty }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $organizations->onEachSide(1)->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </section>

    <!-- Deletion Confirmation Modals -->
    @foreach ($organizations as $organization)
        <x-modal name="delete-organization-{{ $organization->id }}" :show="false" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium">
                    {{ __('Confirm Organization Deletion') }}
                </h2>

                <p class="mt-3 text-sm text-base-content/70">
                    {{ __('Are you sure you want to delete') }} <strong>{{ $organization->name }}</strong>?
                    {{ __('This action cannot be undone and may affect associated users.') }}
                </p>

                <div class="mt-6 flex justify-end gap-3 modal-action">
                    <button x-on:click="$dispatch('close')" class="btn btn-ghost">
                        {{ __('Cancel') }}
                    </button>

                    <form action="{{ route('admin.organizations.destroy', $organization) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">
                            {{ __('Delete Organization') }}
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    @endforeach
</x-app-layout>
