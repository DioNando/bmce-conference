@php
    use App\Enums\UserRole;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li>{{ __('Accounts') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Accounts List') }}
            </h3>
            <div class="flex gap-3">
                <a href="{{ route('admin.users.import.form') }}" class="btn btn-outline">
                    <x-heroicon-s-arrow-up-tray class="size-4" />
                    <div class="hidden lg:block">{{ __('Import') }}</div>
                </a>
                <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-outline">
                    <x-heroicon-s-arrow-down-tray class="size-4" />
                    <span class="hidden lg:block">{{ __('Export') }}</span>
                </a>
                <a href="{{ route('admin.users.scanner') }}" class="btn btn-warning">
                    <x-heroicon-s-qr-code class="size-4" />
                    <span class="hidden lg:block">{{ __('Scan QR Code') }}</span>
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <x-heroicon-s-user-plus class="size-4" />
                    <span class="hidden lg:block">{{ __('Add Account') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <section class="space-y-6" x-data="{
        selectedUsers: [],
        viewMode: localStorage.getItem('usersViewMode') || 'table',
        setViewMode(mode) {
            this.viewMode = mode;
            localStorage.setItem('usersViewMode', mode);
        }
    }">
        @if (session('success'))
            <div role="alert" class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @php
            $headers = [
                ['content' => 'Last name', 'align' => 'text-left', 'sortable' => true, 'sortField' => 'name'],
                ['content' => 'First name', 'align' => 'text-left', 'sortable' => true, 'sortField' => 'first_name'],
                ['content' => 'Contact', 'align' => 'text-left'],
                ['content' => 'Profile', 'align' => 'text-center'],
                ['content' => 'Type', 'align' => 'text-center'],
                ['content' => 'Status', 'align' => 'text-center'],
                [
                    'content' => 'Organization',
                    'align' => 'text-left',
                    'sortable' => true,
                    'sortField' => 'organization_id',
                ],
                ['content' => 'Actions', 'align' => 'text-right'],
            ];
            $empty = 'No users found';

            // Get current sort parameters
            $sortBy = request('sort_by', 'name');
            $sortOrder = request('sort_order', 'asc');
            @endphp

        <div class="card card-bordered bg-base-200 shadow-lg" x-data="{ showFilters: false }">
            <div class="card-body">
                <!-- Search and Filter Controls -->
                <div class="flex justify-between items-center mb-4 gap-4">
                    <div class="form-control w-full max-w-xs">
                        <label class="input">
                            <x-heroicon-s-magnifying-glass class="size-4 opacity-50" />
                            <input type="search" class="grow" name="search" placeholder="Search users..."
                                value="{{ request('search') }}" form="users-filter-form" />
                        </label>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- View toggle buttons -->
                        <button type="button" class="btn rounded-full"
                            :class="{ 'btn-primary': viewMode === 'table', 'btn-ghost': viewMode !== 'table' }"
                            @click="setViewMode('table')">
                            <x-heroicon-s-list-bullet class="size-4" />
                            <span class="hidden sm:inline-block ml-1">{{ __('Table') }}</span>
                        </button>
                        <button type="button" class="btn rounded-full"
                            :class="{ 'btn-primary': viewMode === 'grid', 'btn-ghost': viewMode !== 'grid' }"
                            @click="setViewMode('grid')">
                            <x-heroicon-s-squares-2x2 class="size-4" />
                            <span class="hidden sm:inline-block ml-1">{{ __('Grid') }}</span>
                        </button>
                        <button type="button" class="btn btn-primary" @click="showFilters = !showFilters">
                            <x-heroicon-s-funnel class="size-4" />
                            <span class="hidden lg:block">{{ __('Filters') }}</span>
                            @if (request('search') ||
                                    (request('role') && request('role') != 'all') ||
                                    (request('status') && request('status') != 'all'))
                                <div class="badge badge-sm badge-white rounded-full">{{ __('active') }}</div>
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Expanded Filter Form -->
                <form id="users-filter-form" action="{{ route('admin.users.index') }}" method="GET"
                    x-show="showFilters" x-cloak
                    class="bg-base-100 shadow-md p-4 rounded-box mb-4 transition-all duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">{{ __('Profile') }}</legend>
                            <select id="role" name="role" class="select select-bordered w-full">
                                <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>
                                    {{ __('All Profiles') }}</option>
                                <option value="{{ UserRole::ISSUER->value }}"
                                    {{ request('role') == UserRole::ISSUER->value ? 'selected' : '' }}>
                                    {{ UserRole::ISSUER->label() }}
                                </option>
                                <option value="{{ UserRole::INVESTOR->value }}"
                                    {{ request('role') == UserRole::INVESTOR->value ? 'selected' : '' }}>
                                    {{ UserRole::INVESTOR->label() }}
                                </option>
                            </select>
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">{{ __('Status') }}</legend>
                            <select id="status" name="status" class="select select-bordered w-full">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>
                                    {{ __('All Statuses') }}</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                    {{ __('Active') }}
                                </option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                    {{ __('Inactive') }}
                                </option>
                            </select>
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">{{ __('Sort By') }}</legend>
                            <div class="join w-full">
                                <select name="sort_by" class="select select-bordered join-item w-2/3">
                                    <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>
                                        {{ __('Last name') }}</option>
                                    <option value="first_name" {{ $sortBy === 'first_name' ? 'selected' : '' }}>
                                        {{ __('First name') }}</option>
                                    <option value="organization_id"
                                        {{ $sortBy === 'organization_id' ? 'selected' : '' }}>{{ __('Organization') }}
                                    </option>
                                </select>
                                <select name="sort_order" class="select select-bordered join-item w-1/3">
                                    <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>
                                        {{ __('Asc') }}</option>
                                    <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>
                                        {{ __('Desc') }}</option>
                                </select>
                            </div>
                        </fieldset>
                    </div>

                    <div class="flex gap-3 mt-4 justify-end">
                        @if (request('search') || request('role') != 'all' || request('status') != 'all')
                            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">
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

                <!-- Bulk action section -->
                <div x-show="selectedUsers.length > 0" x-cloak class="mb-4 p-4 bg-base-100 rounded-box shadow">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-lg badge-primary" x-text="selectedUsers.length"></span>
                            <span class="font-medium">{{ __('users selected') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <form action="{{ route('admin.users.toggle-multiple-status') }}" method="POST">
                                @method('PATCH')
                                @csrf
                                <template x-for="userId in selectedUsers" :key="userId">
                                    <input type="hidden" name="user_ids[]" :value="userId">
                                </template>
                                <input type="hidden" name="status" value="1">
                                <button type="submit" class="join-item btn btn-sm btn-success">
                                    <x-heroicon-s-arrow-up class="size-4" />
                                    <span> {{ __('Activate') }}</span>
                                </button>
                            </form>

                            <form action="{{ route('admin.users.toggle-multiple-status') }}" method="POST">
                                @method('PATCH')
                                @csrf
                                <template x-for="userId in selectedUsers" :key="userId">
                                    <input type="hidden" name="user_ids[]" :value="userId">
                                </template>
                                <input type="hidden" name="status" value="0">
                                <button type="submit" class="join-item btn btn-sm btn-error">
                                    <x-heroicon-s-arrow-down class="size-4" />
                                    <span> {{ __('Deactivate') }}</span>
                                </button>
                            </form>

                            <form action="{{ route('admin.users.send-multiple-activation-emails') }}" method="POST">
                                @csrf
                                <template x-for="userId in selectedUsers" :key="userId">
                                    <input type="hidden" name="user_ids[]" :value="userId">
                                </template>
                                <button type="submit" class="join-item btn btn-sm btn-primary">
                                    <x-heroicon-s-envelope class="size-4" />
                                    <span> {{ __('Send activation emails') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div x-data="usersTable()">
                    <!-- Table view mode -->
                    <div x-show="viewMode === 'table'" x-cloak>
                        <x-users.table-view :users="$users" :headers="$headers" :empty="$empty" />
                    </div>

                    <!-- Grid view mode -->
                    <div x-show="viewMode === 'grid'" x-cloak>
                        <x-users.grid-view :users="$users" :empty="$empty" />
                    </div>
                </div>

                <div class="mt-4 flex justify-between items-center gap-2">
                    <div class="text-sm text-base-content/70 flex items-center gap-2">
                        <span>{{ __('Show') }}</span>
                        <div class="w-20">
                            <select id="perPage" name="perPage" class="select select-bordered select-sm"
                                onchange="window.location.href='{{ route('admin.users.index') }}?{{ http_build_query(array_merge(request()->except(['perPage', 'page']), ['perPage' => ''])) }}'+this.value">
                                @foreach ([10, 25, 50, 100] as $option)
                                    <option value="{{ $option }}"
                                        {{ request('perPage', 10) == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <span>{{ __('entries') }}</span>
                    </div>
                    {{-- <span class="mx-2 text-base-content/50">&#8226;</span> --}}
                    {{-- <div class="flex-1"> --}}
                    <div>
                        {{ $users->onEachSide(1)->appends(request()->except('page'))->links('pagination::tailwind') }}
                    </div>
                </div>

                <!-- Statistics Cards -->
                <section class="card bg-base-100 overflow-hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 mt-6 shadow-lg">
                    <!-- Total Users -->
                    <div class="stat">
                        <div class="stat-figure text-secondary">
                            <x-heroicon-s-user-group class="size-8" />
                        </div>
                        <div class="stat-title text-secondary/70">{{ __('Total Users') }}</div>
                        <div class="stat-value text-secondary">{{ number_format($statistics['total_users']) }}</div>
                        <div class="stat-desc text-secondary/60">{{ __('All accounts') }}</div>
                    </div>

                    <!-- Investors -->
                    <div class="stat">
                        <div class="stat-figure text-primary">
                            <x-heroicon-s-briefcase class="size-8" />
                        </div>
                        <div class="stat-title text-primary/70">{{ __('Investors') }}</div>
                        <div class="stat-value text-primary">{{ number_format($statistics['total_investors']) }}</div>
                        <div class="stat-desc text-primary/60">
                            @if ($statistics['total_users'] > 0)
                                {{ round(($statistics['total_investors'] / $statistics['total_users']) * 100, 1) }}%
                                {{ __('of total') }}
                            @else
                                {{ __('0% of total') }}
                            @endif
                        </div>
                    </div>

                    <!-- Issuers -->
                    <div class="stat">
                        <div class="stat-figure text-warning">
                            <x-heroicon-s-building-storefront class="size-8" />
                        </div>
                        <div class="stat-title text-warning/70">{{ __('Issuers') }}</div>
                        <div class="stat-value text-warning">{{ number_format($statistics['total_issuers']) }}</div>
                        <div class="stat-desc text-warning/60">
                            @if ($statistics['total_users'] > 0)
                                {{ round(($statistics['total_issuers'] / $statistics['total_users']) * 100, 1) }}%
                                {{ __('of total') }}
                            @else
                                {{ __('0% of total') }}
                            @endif
                        </div>
                    </div>

                    <!-- Active Users -->
                    <div class="stat">
                        <div class="stat-figure text-success">
                            <x-heroicon-s-check-circle class="size-8" />
                        </div>
                        <div class="stat-title text-success/70">{{ __('Active') }}</div>
                        <div class="stat-value text-success">{{ number_format($statistics['active_users']) }}</div>
                        <div class="stat-desc text-success/60">
                            @if ($statistics['total_users'] > 0)
                                {{ round(($statistics['active_users'] / $statistics['total_users']) * 100, 1) }}%
                                {{ __('of total') }}
                            @else
                                {{ __('0% of total') }}
                            @endif
                        </div>
                    </div>

                    <!-- Inactive Users -->
                    <div class="stat">
                        <div class="stat-figure text-neutral">
                            <x-heroicon-s-x-circle class="size-8" />
                        </div>
                        <div class="stat-title text-neutral/70">{{ __('Inactive') }}</div>
                        <div class="stat-value text-neutral">{{ number_format($statistics['inactive_users']) }}</div>
                        <div class="stat-desc text-neutral/60">
                            @if ($statistics['total_users'] > 0)
                                {{ round(($statistics['inactive_users'] / $statistics['total_users']) * 100, 1) }}%
                                {{ __('of total') }}
                            @else
                                {{ __('0% of total') }}
                            @endif
                        </div>
                    </div>
                </section>
            </div>

        </div>

    </section>

    <script>
        function usersTable() {
            return {
                toggleAll(event) {
                    const checkboxes = document.querySelectorAll('.user-checkbox');
                    const isChecked = event.target.checked;

                    // Vider ou remplir la liste des utilisateurs sélectionnés
                    this.selectedUsers = [];

                    if (isChecked) {
                        // Sélectionner tous les utilisateurs
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = true;
                            this.selectedUsers.push(parseInt(checkbox.value));
                        });
                    } else {
                        // Désélectionner tous les utilisateurs
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });
                    }
                },
                toggleUser(userId) {
                    const index = this.selectedUsers.indexOf(userId);

                    if (index === -1) {
                        // Ajouter à la liste des sélectionnés
                        this.selectedUsers.push(userId);
                    } else {
                        // Retirer de la liste des sélectionnés
                        this.selectedUsers.splice(index, 1);
                    }

                    // Mettre à jour la case "Select All"
                    const checkboxes = document.querySelectorAll('.user-checkbox');
                    const selectAllCheckbox = document.getElementById('select-all-users');
                    selectAllCheckbox.checked = this.selectedUsers.length === checkboxes.length;
                }
            }
        }
    </script>

    <!-- Deletion Confirmation Modals -->
    @foreach ($users as $user)
        <x-modal name="delete-user-{{ $user->id }}" :show="false" focusable>
            <div class="p-6">
                <h2 class="text-lg font-medium">
                    {{ __('Confirm User Deletion') }}
                </h2>

                <p class="mt-3 text-sm text-base-content/70">
                    {{ __('Are you sure you want to delete') }} <strong>{{ $user->first_name }}
                        {{ $user->name }}</strong>?
                    {{ __('This action cannot be undone.') }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button class="btn btn-ghost" x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </button>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">
                            {{ __('Delete User') }}
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    @endforeach
</x-app-layout>
