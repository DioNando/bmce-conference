@php
    use App\Enums\MeetingStatus;

    // Fonction d'aide pour générer les liens de tri
function getSortUrl($field, $currentSortBy, $currentSortOrder)
{
    $newOrder = $currentSortBy === $field && $currentSortOrder === 'asc' ? 'desc' : 'asc';
    return request()->fullUrlWithQuery([
        'sort_by' => $field,
        'sort_order' => $newOrder,
    ]);
}

// Fonction pour obtenir l'icône de tri appropriée
    function getSortIcon($field, $currentSortBy, $currentSortOrder)
    {
        if ($currentSortBy !== $field) {
            return 'M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z M4.22 10.22a.75.75 0 1 1 1.06-1.06L8 11.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 11.28a.75.75 0 0 1 0-1.06Z';
        } else {
            return $currentSortOrder === 'asc'
                ? 'M4.22 15.22a.75.75 0 0 1 1.06 0L8 18.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 16.28a.75.75 0 0 1 0-1.06Z'
                : 'M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z';
        }
    }
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li>{{ __('Meetings') }}</li>
            </ul>
        </div>
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('Meetings Management') }}
            </h3>
            <div class="flex gap-3">
                <a href="{{ route('admin.meetings.export', request()->query()) }}" class="btn btn-outline">
                    <x-heroicon-s-arrow-down-tray class="size-4" />

                    <span class="hidden lg:block">{{ __('Export') }}</span>
                </a>
                <a href="{{ route('admin.meetings.create') }}" class="btn btn-primary">
                    <x-heroicon-s-calendar class="size-4" />
                    <span class="hidden lg:block">{{ __('Add Meeting') }}</span>
                </a>
            </div>
        </div>
    </x-slot>
    <section class="space-y-6" x-data="{
        showFilters: false,
        viewMode: localStorage.getItem('meetings-view-mode') || 'table',
        selectedDate: '{{ $meetings->count() > 0 ? $meetings->first()->timeSlot->date->format('Y-m-d') : '' }}',

        init() {
            this.$watch('viewMode', value => {
                localStorage.setItem('meetings-view-mode', value);
            });
        }
    }">
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
                <!-- Search and Filter Form -->
                <div class="flex justify-between items-center mb-4 gap-4">
                    <div class="form-control w-full max-w-xs">
                        <label class="input">
                            <x-heroicon-s-magnifying-glass class="size-4 opacity-50" />
                            <input type="search" class="grow" name="search" placeholder="Search meetings..."
                                value="{{ request('search') }}" form="meetings-filter-form">
                        </label>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- View Toggle Buttons -->
                        <button type="button" class="btn rounded-full"
                            :class="viewMode === 'table' ? 'btn-primary' : 'btn-ghost'" @click="viewMode = 'table'">
                            <x-heroicon-s-list-bullet class="size-4" />
                            <span class="hidden sm:inline">{{ __('Table') }}</span>
                        </button>
                        <button type="button" class="btn rounded-full"
                            :class="viewMode === 'grid' ? 'btn-primary' : 'btn-ghost'" @click="viewMode = 'grid'">
                            <x-heroicon-s-squares-2x2 class="size-4" />
                            <span class="hidden sm:inline">{{ __('Grid') }}</span>
                        </button>
                        <button type="button" class="btn rounded-full"
                            :class="viewMode === 'calendar' ? 'btn-primary' : 'btn-ghost'" @click="viewMode = 'calendar'">
                            <x-heroicon-s-calendar-days class="size-4" />
                            <span class="hidden sm:inline">{{ __('Calendar') }}</span>
                        </button>
                        <button type="button" class="btn btn-primary" @click="showFilters = !showFilters">
                            <x-heroicon-s-funnel class="size-4" />
                            <span class="hidden lg:block">{{ __('Filters') }}</span>
                            @if (request('search') ||
                                    (request('date') && request('date') != 'all') ||
                                    (request('issuer_id') && request('issuer_id') != 'all') ||
                                    (request('format') && request('format') != 'all') ||
                                    (request('status') && request('status') != 'all'))
                                <div class="badge badge-sm badge-white rounded-full">{{ __('active') }}</div>
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Expanded Filter Form -->
                <form id="meetings-filter-form" action="{{ route('admin.meetings.index') }}" method="GET"
                    x-show="showFilters" x-cloak
                    class="bg-base-100 shadow-md p-4 rounded-box mb-4 transition-all duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Date') }}</legend>
                                <select id="date" name="date" class="select select-bordered w-full">
                                    <option value="all" {{ request('date') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Dates') }}</option>
                                    @foreach ($dates as $date)
                                        <option value="{{ $date }}"
                                            {{ request('date') == $date ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Issuer') }}</legend>
                                <select id="issuer" name="issuer_id" class="select select-bordered w-full">
                                    <option value="all" {{ request('issuer_id') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Issuers') }}</option>
                                    @foreach ($issuers as $issuer)
                                        <option value="{{ $issuer->id }}"
                                            {{ request('issuer_id') == $issuer->id ? 'selected' : '' }}>
                                            {{ $issuer->first_name }} {{ $issuer->name }}
                                            ({{ $issuer->organization->name ?? 'No Org' }})
                                        </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Room') }}</legend>
                                <select id="room" name="room_id" class="select select-bordered w-full">
                                    <option value="all" {{ request('room_id') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Rooms') }}</option>
                                    <option value="null" {{ request('room_id') == 'null' ? 'selected' : '' }}>
                                        {{ __('No Room') }}</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->name }} ({{ $room->location }})
                                        </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Format') }}</legend>
                                <select id="format" name="format" class="select select-bordered w-full">
                                    <option value="all" {{ request('format') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Formats') }}</option>
                                    <option value="1" {{ request('format') == '1' ? 'selected' : '' }}>
                                        {{ __('1-on-1') }}</option>
                                    <option value="0" {{ request('format') == '0' ? 'selected' : '' }}>
                                        {{ __('Group') }}</option>
                                </select>
                            </fieldset>
                        </div>
                        <div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">{{ __('Status') }}</legend>
                                <select id="status" name="status" class="select select-bordered w-full">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Statuses') }}</option>
                                    @foreach (App\Enums\MeetingStatus::all() as $status)
                                        <option value="{{ $status->value }}"
                                            {{ request('status') == $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4 justify-end">
                        @if (request('search') ||
                                request('date') != 'all' ||
                                request('issuer_id') != 'all' ||
                                request('room_id') != 'all' ||
                                request('format') != 'all' ||
                                request('status') != 'all')
                            <a href="{{ route('admin.meetings.index') }}" class="btn btn-ghost">
                                {{ __('Reset Filters') }}
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            {{ __('Apply Filters') }}
                        </button>
                    </div>

                    <!-- Champs cachés pour conserver les valeurs de tri lors d'autres filtres -->
                    <input type="hidden" name="sort_by" value="{{ $sortBy ?? 'time_slots.date' }}">
                    <input type="hidden" name="sort_order" value="{{ $sortOrder ?? 'desc' }}">
                </form>



                @php
                    $headers = [
                        [
                            'content' => 'ID',
                            'align' => 'text-left',
                            'sortable' => true,
                            'sortField' => 'id',
                        ],
                        [
                            'content' => 'Date/Time',
                            'align' => 'text-left',
                            'sortable' => true,
                            'sortField' => 'time_slots.date',
                        ],
                        ['content' => 'Room', 'align' => 'text-left'],
                        ['content' => 'Issuer', 'align' => 'text-left'],
                        ['content' => 'Investors', 'align' => 'text-center'],
                        ['content' => 'Format', 'align' => 'text-center'],
                        ['content' => 'Status', 'align' => 'text-center'],
                        ['content' => 'Questions', 'align' => 'text-center'],
                        [
                            'content' => 'Created At',
                            'align' => 'text-center',
                            'sortable' => true,
                            'sortField' => 'created_at',
                        ],
                        ['content' => 'Actions', 'align' => 'text-right'],
                    ];
                    $empty = 'No meetings found';
                @endphp

                <!-- Table View -->
                <div x-show="viewMode === 'table'" x-cloak>
                    <x-meetings.table-view :meetings="$meetings" :headers="$headers" :empty="$empty" />
                </div>

                <!-- Grid View -->
                <div x-show="viewMode === 'grid'" x-cloak>
                    <x-meetings.grid-view :meetings="$meetings" :empty="$empty" />
                </div>

                <!-- Calendar View -->
                <div x-show="viewMode === 'calendar'" x-cloak>
                    <x-meetings.calendar-view :meetings="$meetings" :empty="$empty" />
                </div>

                <div class="mt-4 flex justify-between items-center gap-2">
                    <div class="text-sm text-base-content/70 flex items-center gap-2">
                        <span>{{ __('Show') }}</span>
                        <div class="w-20">
                            <select id="perPage" name="perPage"
                                class="select select-bordered select-sm w-full max-w-xs"
                                onchange="window.location.href='{{ route('admin.meetings.index') }}?{{ http_build_query(array_merge(request()->except(['perPage', 'page']), ['perPage' => ''])) }}'+this.value">
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
                    <div>
                        {{ $meetings->onEachSide(1)->appends(request()->except('page'))->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Deletion Confirmation Modals -->
    @foreach ($meetings as $meeting)
        <x-modal name="delete-meeting-{{ $meeting->id }}" :show="false" focusable>
            <div class="p-6">
                <h3 class="text-lg font-medium text-error">
                    {{ __('Confirm Meeting Deletion') }}
                </h3>

                <p class="mt-3 text-sm text-base-content/70">
                    {{ __('Are you sure you want to delete the meeting with') }}
                    <strong>{{ $meeting->issuer->first_name }} {{ $meeting->issuer->name }}</strong>
                    {{ __('on') }} <strong>{{ $meeting->timeSlot->date->format('l, F j, Y') }}</strong>?
                    {{ __('This action cannot be undone.') }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </button>

                    <form action="{{ route('admin.meetings.destroy', $meeting) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">
                            {{ __('Delete Meeting') }}
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    @endforeach
    </section>
</x-app-layout>
