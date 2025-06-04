<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('issuer.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li>{{ __('Meetings') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                {{ __('My Meetings') }}
            </h3>
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

        <div class="card card-bordered bg-base-200 shadow-lg" x-data="{
            showFilters: false,
            viewMode: localStorage.getItem('issuer-meetings-view-mode') || 'table',
            selectedDate: '{{ $meetings->count() > 0 ? $meetings->first()->timeSlot->date->format('Y-m-d') : '' }}',

            init() {
                this.$watch('viewMode', value => {
                    localStorage.setItem('issuer-meetings-view-mode', value);
                });
            }
        }">
            <div class="card-body">
                <!-- Search and Filter Form -->
                <div class="flex justify-between items-center mb-4 gap-4">
                    <div class="form-control w-full max-w-xs">
                        <label class="input">
                            <x-heroicon-s-magnifying-glass class="size-4 opacity-50" />
                            <input type="search" class="grow" name="search"
                                placeholder="{{ __('Search meetings...') }}" value="{{ request('search') }}"
                                form="meetings-filter-form">
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
                            :class="viewMode === 'calendar' ? 'btn-primary' : 'btn-ghost'" @click="viewMode = 'calendar'">
                            <x-heroicon-s-calendar-days class="size-4" />
                            <span class="hidden sm:inline">{{ __('Calendar') }}</span>
                        </button>
                        <button type="button" class="btn btn-primary" @click="showFilters = !showFilters">
                            <x-heroicon-s-funnel class="size-4" />
                            <span class="hidden lg:block">{{ __('Filters') }}</span>
                            @if (request('search') ||
                                    (request('date') && request('date') != 'all') ||
                                    (request('status') && request('status') != 'all') ||
                                    (request('format') && request('format') != 'all'))
                                <div class="badge badge-sm badge-white rounded-full">{{ __('active') }}</div>
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Expanded Filter Form -->
                <form id="meetings-filter-form" action="{{ route('issuer.meetings.index') }}" method="GET"
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
                                <legend class="fieldset-legend">{{ __('Status') }}</legend>
                                <select id="status" name="status" class="select select-bordered w-full">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>
                                        {{ __('All Statuses') }}</option>
                                    @foreach (\App\Enums\MeetingStatus::cases() as $meetingStatus)
                                        <option value="{{ $meetingStatus->value }}"
                                            {{ request('status') == $meetingStatus->value ? 'selected' : '' }}>
                                            {{ __($meetingStatus->label()) }}
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
                                        {{ __('One-on-One') }}</option>
                                    <option value="0" {{ request('format') == '0' ? 'selected' : '' }}>
                                        {{ __('Group') }}</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4 justify-end">
                        @if (request('search') ||
                                (request('date') && request('date') != 'all') ||
                                (request('status') && request('status') != 'all') ||
                                (request('format') && request('format') != 'all'))
                            <a href="{{ route('issuer.meetings.index') }}" class="btn btn-ghost">
                                {{ __('Reset Filters') }}
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <span>{{ __('Apply Filters') }}</span>
                        </button>
                    </div>
                </form>

                @php
                    $headers = [
                        ['content' => 'Date/Time', 'align' => 'text-left'],
                        ['content' => 'Room', 'align' => 'text-left'],
                        ['content' => 'Investors', 'align' => 'text-center'],
                        ['content' => 'Format', 'align' => 'text-center'],
                        ['content' => 'Status', 'align' => 'text-center'],
                        ['content' => 'Questions', 'align' => 'text-center'],
                        ['content' => 'Actions', 'align' => 'text-right'],
                    ];
                    $empty = 'No meetings found';
                @endphp

                <!-- Table View -->
                <div x-show="viewMode === 'table'" x-cloak>
                    <x-meetings.issuer-table-view :meetings="$meetings" :headers="$headers" :empty="$empty" />
                </div>

                <!-- Calendar View -->
                <div x-show="viewMode === 'calendar'" x-cloak>
                    <x-meetings.issuer-calendar-view :meetings="$meetings" :empty="$empty" />
                </div>

                <div class="mt-4 flex justify-between items-center gap-2" x-show="viewMode === 'table'" x-cloak>
                    <div class="text-sm text-base-content/70 flex items-center gap-2">
                        <span>{{ __('Show') }}</span>
                        <div class="w-20">
                            <select id="perPage" name="perPage"
                                class="select select-bordered select-sm w-full max-w-xs"
                                onchange="window.location.href='{{ route('issuer.meetings.index') }}?{{ http_build_query(array_merge(request()->except(['perPage', 'page']), ['perPage' => ''])) }}'+this.value">
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
</x-app-layout>
