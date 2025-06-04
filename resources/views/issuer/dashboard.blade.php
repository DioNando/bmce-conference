<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li class="text-primary">{{ __('Issuer Dashboard') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <!-- Header with User Info -->
        <div class="flex flex-col lg:flex-row lg:items-center gap-6">
            <!-- User Info Card -->
            <div class="w-fit card card-border bg-base-100 shadow-lg p-6">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if (Auth::user()->profile_photo_path)
                                <img class="h-16 w-16 rounded-full object-cover"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                            @else
                                <div
                                    class="h-16 w-16 rounded-full bg-warning/10 flex items-center justify-center text-warning text-2xl font-mono font-semibold">
                                    {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-base-content">
                                {{ Auth::user()->first_name . ' ' . Auth::user()->name }}</h4>
                            <p class="text-base-content/80">{{ Auth::user()->email }}</p>
                            <p class="text-sm text-base-content/60">{{ __('Organization') }}:
                                {{ Auth::user()->organization->name ?? __('Not Specified') }}</p>
                        </div>
                    </div>
                    <span class="badge badge-warning badge-lg">
                        {{ __('Issuer') }}
                    </span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('issuer.schedule') }}"
                    class="card card-bordered bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-warning">{{ __('Manage Availability') }}</h4>
                            <x-heroicon-s-clock class="size-8 text-warning/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">
                            {{ __('Set your available time slots for meetings') }}
                        </p>
                    </div>
                </a>

                <a href="{{ route('issuer.meetings.index') }}"
                    class="card card-bordered bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-primary">{{ __('My Meetings') }}</h4>
                            <x-heroicon-s-user-group class="size-8 text-primary/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">{{ __('View and manage your meeting schedule') }}
                        </p>
                    </div>
                </a>

                <a href="{{ route('issuer.qr-code.show') }}"
                    class="card card-bordered bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-neutral">{{ __('My QR Code') }}</h4>
                            <x-heroicon-s-qr-code class="size-8 text-accent/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">
                            {{ __('View and download your personal QR code') }}
                        </p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div>
            <h3 class="flex items-center gap-2 text-2xl font-bold text-warning mb-6">
                <x-heroicon-s-chart-bar class="w-6 h-6" />
                {{ __('Key Performance Indicators') }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Meetings -->
                <div class="card bg-gradient-to-br from-primary/5 to-primary/10 border border-primary/20 shadow-lg">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-primary/80 font-medium">{{ __('Total Meetings') }}</p>
                                <p class="text-3xl font-bold text-primary">
                                    {{ $scheduledMeetings->count() + $completedMeetings->count() + $cancelledMeetings->count() + $pendingMeetings->count() }}
                                </p>
                                <p class="text-sm text-primary/60">{{ __('All time meetings') }}</p>
                            </div>
                            <div class="stat-figure text-primary/40">
                                <x-heroicon-s-calendar-days class="size-12" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meeting Attendance Rate -->
                <div class="card bg-gradient-to-br from-success/5 to-success/10 border border-success/20 shadow-lg">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-success/80 font-medium">{{ __('Attendance Rate') }}</p>
                                <p class="text-3xl font-bold text-success">{{ $meetingAttendanceRate }}%</p>
                                <p class="text-sm text-success/60">{{ __('Successful meetings') }}</p>
                            </div>
                            <div class="stat-figure text-success/40">
                                <x-heroicon-s-check-circle class="size-12" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Received -->
                <div class="card bg-gradient-to-br from-accent/5 to-accent/10 border border-accent/20 shadow-lg">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-accent/80 font-medium">{{ __('Questions Received') }}</p>
                                <p class="text-3xl font-bold text-accent">{{ $totalQuestions }}</p>
                                <p class="text-sm text-accent/60">{{ $questionResponseRate }}% {{ __('answered') }}
                                </p>
                            </div>
                            <div class="stat-figure text-accent/40">
                                <x-heroicon-s-question-mark-circle class="size-12" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investors Met -->
                <div class="card bg-gradient-to-br from-primary/5 to-primary/10 border border-primary/20 shadow-lg">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-primary/80 font-medium">{{ __('Investors Met') }}</p>
                                <p class="text-3xl font-bold text-primary">{{ $totalInvestorsMet }}</p>
                                <p class="text-sm text-primary/60">{{ __('Unique investors') }}</p>
                            </div>
                            <div class="stat-figure text-primary/40">
                                <x-heroicon-s-briefcase class="size-12" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div>
            <h3 class="flex items-center gap-2 text-xl font-bold text-base-content mb-6">
                <x-heroicon-s-chart-pie class="w-5 h-5" />
                {{ __('Detailed Analytics') }}
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Meeting Status Breakdown -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h4 class="card-title text-accent mb-4 flex items-center gap-2">
                            <x-heroicon-s-chart-bar class="w-5 h-5" />
                            {{ __('Meeting Status Overview') }}
                        </h4>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="card bg-primary/10 border border-primary/20 p-3">
                                <div class="stat-title text-xs">{{ __('Upcoming') }}</div>
                                <div class="stat-value text-lg text-primary">{{ $upcomingMeetings->count() }}</div>
                            </div>
                            <div class="card bg-success/10 border border-success/20 p-3">
                                <div class="stat-title text-xs">{{ __('Completed') }}</div>
                                <div class="stat-value text-lg text-success">{{ $completedMeetings->count() }}</div>
                            </div>
                            <div class="card bg-warning/10 border border-warning/20 p-3">
                                <div class="stat-title text-xs">{{ __('Pending') }}</div>
                                <div class="stat-value text-lg text-warning">{{ $pendingMeetings->count() }}</div>
                            </div>
                            <div class="card bg-error/10 border border-error/20 p-3">
                                <div class="stat-title text-xs">{{ __('Cancelled') }}</div>
                                <div class="stat-value text-lg text-error">{{ $cancelledMeetings->count() }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-2">
                            <div class="stat-desc">{{ __('Meeting Success Rate') }}</div>
                            <div class="text-2xl font-bold text-success">{{ $meetingAttendanceRate }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Questions & Engagement -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h4 class="card-title text-secondary mb-4 flex items-center gap-2">
                            <x-heroicon-s-chat-bubble-left-right class="w-5 h-5" />
                            {{ __('Questions & Engagement') }}
                        </h4>

                        <div class="space-y-4 mt-4">
                            <div class="card overflow-hidden grid grid-cols-2 mt-4">
                                <div class="stat bg-success/10 p-3">
                                    <div class="stat-title text-xs">{{ __('Answered') }}</div>
                                    <div class="stat-value text-lg text-success">{{ $answeredQuestions }}</div>
                                </div>
                                <div class="stat bg-warning/10 p-3">
                                    <div class="stat-title text-xs">{{ __('Pending') }}</div>
                                    <div class="stat-value text-lg text-warning">{{ $unansweredQuestions }}</div>
                                </div>
                            </div>

                            <!-- Response Rate -->
                            <div class="mt-4">
                                <div class="flex justify-between text-sm">
                                    <span>{{ __('Response Rate') }}</span>
                                    <span class="font-semibold">{{ $questionResponseRate }}%</span>
                                </div>
                                <progress class="progress progress-success" value="{{ $questionResponseRate }}"
                                    max="100"></progress>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Network & Availability -->
        <div>
            <h3 class="flex items-center gap-2 text-xl font-bold text-base-content mb-6">
                <x-heroicon-s-globe-alt class="w-5 h-5" />
                {{ __('Network & Availability') }}
            </h3>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Investor Network Coverage -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h4 class="card-title text-neutral mb-4">
                            <x-heroicon-s-user-group class="w-5 h-5" />
                            {{ __('Investor Network') }}
                        </h4>

                        <div class="h-full flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">{{ __('Investors Met') }}</span>
                                    <span class="font-bold">{{ $totalInvestorsMet }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">{{ __('Total Active Investors') }}</span>
                                    <span class="font-bold">{{ $totalActiveInvestors }}</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                {{-- <div class="divider my-2"></div> --}}
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-base-content/60">{{ __('Network Coverage') }}</div>
                                    <div class="text-xl font-bold text-neutral">
                                        {{ $totalActiveInvestors > 0 ? round(($totalInvestorsMet / $totalActiveInvestors) * 100, 1) : 0 }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organizations Reached -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h4 class="card-title text-green-600 mb-4">
                            <x-heroicon-s-building-office class="w-5 h-5" />
                            {{ __('Organizations') }}
                        </h4>

                        <div class="h-full flex flex-col justify-between">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-success">{{ $organizationsCovered }}</div>
                                <div class="text-sm text-base-content/60">{{ __('Organizations reached') }}</div>
                            </div>
                            <div class="mt-4">
                                {{-- <div class="divider my-2"></div> --}}
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-base-content/60">{{ __('Diversification') }}</div>
                                    <div class="badge badge-success">
                                        {{ $organizationsCovered > 5 ? __('Excellent') : ($organizationsCovered > 2 ? __('Good') : __('Limited')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time Slot Management -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h4 class="card-title text-orange-600 mb-4">
                            <x-heroicon-s-clock class="w-5 h-5" />
                            {{ __('Time Slots') }}
                        </h4>

                        <div class="h-full flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">{{ __('Available') }}</span>
                                    <span class="font-bold text-success">{{ $availableTimeSlots }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">{{ __('Booked') }}</span>
                                    <span class="font-bold text-warning">{{ $bookedTimeSlots }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm">{{ __('Total') }}</span>
                                    <span class="font-bold">{{ $totalTimeSlots }}</span>
                                </div>
                            </div>
                            <div>
                                {{-- <div class="divider my-2"></div> --}}
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-base-content/60">{{ __('Availability Rate') }}</div>
                                    <div class="text-xl font-bold text-primary">
                                        {{ $totalTimeSlots > 0 ? round(($availableTimeSlots / $totalTimeSlots) * 100, 1) : 0 }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Meetings -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h3 class="flex items-center gap-2 text-xl font-bold text-primary">
                    <x-heroicon-s-calendar class="w-5 h-5" />
                    {{ __('Next Meetings') }}
                </h3>
                @if ($upcomingMeetingsForDisplay->count() > 0)
                    <a href="{{ route('issuer.meetings.index') }}" class="btn btn-outline btn-primary btn-sm">
                        {{ __('View All') }}
                    </a>
                @endif
            </div>

            <div class="card overflow-hidden bg-base-100 shadow-lg">
                @if ($upcomingMeetingsForDisplay->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Investors') }}</th>
                                    <th>{{ __('Date & Time') }}</th>
                                    <th>{{ __('Location') }}</th>
                                    <th class="text-center">{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($upcomingMeetingsForDisplay as $meeting)
                                    <tr
                                        class="{{ $meeting->timeSlot && $meeting->timeSlot->start_time->diffInHours(now()) < 24 ? 'bg-warning/10' : '' }}">
                                        <td>
                                            <div class="font-medium">
                                                @if ($meeting->investors->count() > 1)
                                                    {{ $meeting->investors->count() }} {{ __('investors') }}
                                                @elseif($meeting->investors->count() == 1)
                                                    {{ $meeting->investors->first()->name . ' ' . $meeting->investors->first()->first_name }}
                                                @else
                                                    {{ __('No investors yet') }}
                                                @endif
                                            </div>
                                            <div class="text-base-content/60 text-sm">
                                                @if ($meeting->investors->count() == 1)
                                                    {{ $meeting->investors->first()->organization->name ?? '-' }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div>
                                                    <div class="font-medium">
                                                        {{ $meeting->timeSlot->start_time->format('d M Y') }}
                                                    </div>
                                                    <div class="text-base-content/60 text-sm">
                                                        {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                        {{ $meeting->timeSlot->end_time->format('H:i') }}
                                                    </div>
                                                </div>
                                                @if ($meeting->timeSlot->start_time->diffInHours(now()) < 24)
                                                    <span
                                                        class="badge badge-warning badge-xs ml-1">{{ __('Soon') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-medium">{{ $meeting->room->name ?? __('Not assigned') }}
                                            </div>
                                            <div class="text-base-content/60 text-sm">
                                                {{ $meeting->room ? $meeting->room->location : __('Location to be determined') }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-{{ $meeting->status->color() }}">{{ $meeting->status->label() }}</span>
                                        </td>
                                        <td>
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('issuer.meetings.show', $meeting->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <x-heroicon-s-eye class="size-4" />
                                                    {{ __('View') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <x-heroicon-s-calendar class="mx-auto size-12 text-base-content/30" />
                        <h3 class="mt-2 text-lg font-semibold text-base-content">{{ __('No Upcoming Meetings') }}
                        </h3>
                        <p class="mt-1 text-base-content/70">
                            {{ __('Start by setting your availability to allow investors to schedule meetings.') }}
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('issuer.schedule') }}" class="btn btn-warning">
                                <x-heroicon-s-clock class="size-4" />
                                {{ __('Manage Availability') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
