@props(['meetings', 'empty' => 'No meetings found'])

@php
    // Group meetings by date and time slot
    $meetingsByDate = $meetings->groupBy(function ($meeting) {
        return $meeting->timeSlot->date->format('Y-m-d');
    });

    // Get all unique dates from the meetings
    $dates = $meetingsByDate->keys()->sort();

    // Define time slots for calendar grid
    $timeSlots = [
        '09:00' => '09:00 - 09:45',
        '10:00' => '10:00 - 10:45',
        '11:00' => '11:00 - 11:45',
        '12:00' => '12:00 - 12:45',
        '14:00' => '14:00 - 14:45',
        '15:00' => '15:00 - 15:45',
        '16:00' => '16:00 - 16:45',
        '17:00' => '17:00 - 17:45',
    ];
@endphp

<div>
    @if ($dates->count() > 0)
        <!-- Date Navigation -->
        <div class="flex justify-end mb-6">
            <div class="w-fit-end card bg-base-100 shadow-md">
                <div class="card-body p-4">
                    <div class="flex flex-wrap gap-2 justify-center">
                        @foreach ($dates as $date)
                            @php
                                $carbonDate = \Carbon\Carbon::parse($date);
                                $meetingsCount = $meetingsByDate[$date]->count();
                            @endphp
                            <div class="flex flex-col items-center gap-1">
                                <button type="button" class="btn calendar-date-btn rounded-full"
                                    data-date="{{ $date }}"
                                    :class="selectedDate === '{{ $date }}' ? 'btn-primary' : 'btn-ghost'"
                                    @click="selectedDate = '{{ $date }}'">
                                    <div class="text-center flex items-center gap-1.5">
                                        <span class="font-medium">{{ $carbonDate->format('M j') }}</span>
                                        <span class="text-xs opacity-70">{{ $carbonDate->format('D') }}</span>
                                        <span class="badge badge-xs rounded-full" :class="selectedDate === '{{ $date }}' ? 'badge-white' : 'badge-primary'">{{ $meetingsCount }}</span>
                                    </div>
                                </button>
                                {{-- <div>
                                <span class="text-xs opacity-70">{{ $carbonDate->format('D') }}</span>
                                <span class="badge badge-primary badge-xs">{{ $meetingsCount }}</span>
                            </div> --}}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Grid for each date -->
        @foreach ($dates as $date)
            @php
                $carbonDate = \Carbon\Carbon::parse($date);
                $dayMeetings = $meetingsByDate[$date];

                // Group meetings by time slot
                $meetingsByTimeSlot = $dayMeetings->groupBy(function ($meeting) {
                    return $meeting->timeSlot->start_time->format('H:i');
                });
            @endphp

            <div x-show="selectedDate === '{{ $date }}'" x-cloak class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <!-- Date Header -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-primary">
                                {{ $carbonDate->translatedFormat('l j F Y') }}
                            </h3>
                            <p class="text-base-content/70">
                                {{ trans_choice('{0} No meetings|{1} :count meeting|[2,*] :count meetings', $dayMeetings->count(), ['count' => $dayMeetings->count()]) }}
                            </p>
                        </div>
                        <div class="stats shadow">
                            <div class="stat px-4 py-2 text-center">
                                <div class="stat-title text-xs">{{ __('Total') }}</div>
                                <div class="stat-value text-primary text-lg">{{ $dayMeetings->count() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach ($timeSlots as $timeKey => $timeLabel)
                            @php
                                $slotMeetings = $meetingsByTimeSlot->get($timeKey, collect());
                            @endphp

                            <div
                                class="card card-bordered {{ $slotMeetings->count() > 0 ? 'bg-primary/5 border-primary/20' : 'bg-base-200' }}">
                                <div class="card-body p-4">
                                    <!-- Time Slot Header -->
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-semibold text-sm text-base-content/80">
                                            {{ $timeLabel }}
                                        </h4>
                                        @if ($slotMeetings->count() > 0)
                                            <div class="badge badge-primary badge-sm">
                                                {{ $slotMeetings->count() }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Meetings in this slot -->
                                    @if ($slotMeetings->count() > 0)
                                        <div class="space-y-3">
                                            @foreach ($slotMeetings as $meeting)
                                                <div
                                                    class="card card-bordered bg-base-100 shadow-sm hover:shadow-md transition-all duration-200">
                                                    <div class="card-body p-3">
                                                        <!-- Meeting Header -->
                                                        <div class="flex justify-between items-start mb-2">
                                                            <div class="flex items-center gap-2">
                                                                <div class="badge badge-neutral text-xs">
                                                                    #{{ $meeting->id }}</div>
                                                                <div
                                                                    class="badge badge-{{ $meeting->status->color() }} badge-xs">
                                                                    {{ $meeting->status->label() }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Issuer Info -->
                                                        <div class="mb-2">
                                                            <div class="flex items-center gap-2 text-sm">
                                                                <x-heroicon-s-building-office
                                                                    class="size-3 text-primary/70 flex-shrink-0" />
                                                                <span class="font-medium truncate">
                                                                    {{ $meeting->issuer->name }}
                                                                    {{ $meeting->issuer->first_name }}
                                                                </span>
                                                            </div>
                                                            @if ($meeting->issuer->organization)
                                                                <div class="text-xs text-base-content/60 ml-5">
                                                                    {{ $meeting->issuer->organization->name }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Meeting Details -->
                                                        <div class="space-y-1 text-xs text-base-content/70">
                                                            @if ($meeting->room)
                                                                <div class="flex items-center gap-2">
                                                                    <x-heroicon-s-map-pin
                                                                        class="size-3 flex-shrink-0" />
                                                                    <span
                                                                        class="truncate">{{ $meeting->room->name }}</span>
                                                                </div>
                                                            @endif

                                                            <div class="flex items-center gap-2">
                                                                <x-heroicon-s-user-group class="size-3 flex-shrink-0" />
                                                                <span>{{ $meeting->investors_count }}
                                                                    {{ __('investors') }}</span>
                                                            </div>

                                                            @if ($meeting->is_one_on_one)
                                                                <div class="flex items-center gap-2">
                                                                    <x-heroicon-s-chat-bubble-left-right
                                                                        class="size-3 flex-shrink-0" />
                                                                    <span>{{ __('1-on-1') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Actions -->
                                                        <div class="flex justify-between gap-2 mt-3">
                                                            <a href="{{ route('admin.meetings.show', $meeting) }}"
                                                                class="btn btn-primary btn-xs rounded-full">
                                                                <x-heroicon-s-eye class="size-3" />
                                                                {{ __('View Details') }}
                                                            </a>
                                                            <a href="{{ route('admin.meetings.edit', $meeting) }}"
                                                                class="btn btn-success btn-xs btn-square">
                                                                <x-heroicon-s-pencil class="size-3" />
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-base-content/50 py-4">
                                            <x-heroicon-s-calendar-days class="size-8 mx-auto mb-2 opacity-30" />
                                            <p class="text-xs">{{ __('No meetings') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Empty State -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body text-center py-12">
                <div class="mx-auto w-24 h-24 bg-base-200 rounded-full flex items-center justify-center mb-4">
                    <x-heroicon-s-calendar class="size-12 text-base-content/30" />
                </div>
                <h3 class="text-lg font-medium text-base-content mb-2">{{ $empty }}</h3>
                <p class="text-sm text-base-content/70 max-w-md mx-auto">
                    {{ __('No meetings are currently scheduled. Create your first meeting to get started.') }}
                </p>
            </div>
        </div>
    @endif
</div>
