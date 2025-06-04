<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li class="text-primary">{{ __('Investor Dashboard') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
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
                                    class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center text-primary text-2xl font-mono font-semibold">
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
                    <span class="badge badge-primary badge-lg">
                        {{ __('Investor') }}
                    </span>
                </div>
            </div>

            <!-- Dashboard Quick Actions -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('investor.issuers.index') }}"
                    class="card card-bordered bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-secondary">{{ __('Browse Issuers') }}</h4>
                            <x-heroicon-s-building-office class="size-8 text-secondary/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">
                            {{ __('Explore companies presenting at the conference') }}</p>
                    </div>
                </a>

                <a href="{{ route('investor.meetings.index') }}"
                    class="card card-bordered bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-primary">{{ __('My Meetings') }}</h4>
                            <x-heroicon-s-calendar class="size-8 text-primary/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">{{ __('View and manage your meeting schedule') }}
                        </p>
                    </div>
                </a>

                <a href="{{ route('investor.qr-code.show') }}"
                    class="card card-bordered bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-neutral">{{ __('My QR Code') }}</h4>
                            <x-heroicon-s-qr-code class="size-8 text-accent/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">
                            {{ __('View and download your personal QR code') }}</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                <x-heroicon-s-chart-bar class="size-6" />
                {{ __('My Conference Overview') }}
            </h3>
        </div>

        <!-- Main Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Upcoming Meetings -->
            <div class="card bg-gradient-to-br from-primary/5 to-primary/10 border border-primary/20 shadow-lg">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-primary/80 font-medium">{{ __('Upcoming') }}</p>
                            <p class="text-3xl font-bold text-primary">{{ $upcomingMeetings->count() }}</p>
                            <p class="text-sm text-primary/60">{{ __('meetings scheduled') }}</p>
                        </div>
                        <div class="stat-figure text-primary/40">
                            <x-heroicon-s-calendar-days class="size-12" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="card bg-gradient-to-br from-warning/5 to-warning/10 border border-warning/20 shadow-lg">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-warning/80 font-medium">{{ __('Pending') }}</p>
                            <p class="text-3xl font-bold text-warning">{{ $pendingMeetings->count() }}</p>
                            <p class="text-sm text-warning/60">{{ __('awaiting response') }}</p>
                        </div>
                        <div class="stat-figure text-warning/40">
                            <x-heroicon-s-clock class="size-12" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Meetings -->
            <div class="card bg-gradient-to-br from-success/5 to-success/10 border border-success/20 shadow-lg">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-success/80 font-medium">{{ __('Completed') }}</p>
                            <p class="text-3xl font-bold text-success">{{ $completedMeetings->count() }}</p>
                            <p class="text-sm text-success/60">{{ __('meetings held') }}</p>
                        </div>
                        <div class="stat-figure text-success/40">
                            <x-heroicon-s-check-circle class="size-12" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Network Reach -->
            <div class="card bg-gradient-to-br from-accent/5 to-accent/10 border border-accent/20 shadow-lg">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-accent/80 font-medium">{{ __('Network') }}</p>
                            <p class="text-3xl font-bold text-accent">{{ $issuersMetWith }}</p>
                            <p class="text-sm text-accent/60">{{ __('issuers connected') }}</p>
                        </div>
                        <div class="stat-figure text-accent/40">
                            <x-heroicon-s-users class="size-12" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Meeting Status Breakdown -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h4 class="card-title text-accent flex items-center gap-2">
                        <x-heroicon-s-chart-pie class="size-5" />
                        {{ __('Meeting Status Overview') }}
                    </h4>

                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="card bg-accent/10 border border-accent/20 p-3">
                            <div class="stat-title text-xs">{{ __('Confirmed') }}</div>
                            <div class="stat-value text-lg text-accent">{{ $confirmedMeetings->count() }}</div>
                        </div>
                        <div class="card bg-info/10 border border-info/20 p-3">
                            <div class="stat-title text-xs">{{ __('Attended') }}</div>
                            <div class="stat-value text-lg text-info">{{ $attendedMeetings->count() }}</div>
                        </div>
                        <div class="card bg-error/10 border border-error/20 p-3">
                            <div class="stat-title text-xs">{{ __('Cancelled') }}</div>
                            <div class="stat-value text-lg text-error">{{ $cancelledMeetings->count() }}</div>
                        </div>
                    </div>

                    @if ($completedMeetings->count() > 0)
                        <div class="alert alert-info mt-4">
                            <x-heroicon-s-information-circle class="size-5" />
                            <span>
                                {{ __('Meeting effectiveness: :rate%', ['rate' => $meetingEffectiveness]) }}
                                <span class="text-sm">{{ __('(attended vs completed)') }}</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Questions & Engagement -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h4 class="card-title text-secondary flex items-center gap-2">
                        <x-heroicon-s-chat-bubble-left-right class="size-5" />
                        {{ __('Questions & Engagement') }}
                    </h4>

                    <div class="card overflow-hidden grid grid-cols-3 mt-4">
                        <div class="stat bg-secondary/10 p-3">
                            <div class="stat-figure text-secondary">
                                <x-heroicon-s-question-mark-circle class="size-8" />
                            </div>
                            <div class="stat-title">{{ __('Total Questions') }}</div>
                            <div class="stat-value text-secondary">{{ $totalQuestions }}</div>
                            <div class="stat-desc">{{ __('Asked to issuers') }}</div>
                        </div>

                        <div class="stat bg-success/10 p-3">
                            <div class="stat-figure text-success">
                                <x-heroicon-s-check-badge class="size-8" />
                            </div>
                            <div class="stat-title">{{ __('Answered') }}</div>
                            <div class="stat-value text-success">{{ $answeredQuestions }}</div>
                            <div class="stat-desc">{{ __('Responses received') }}</div>
                        </div>

                        <div class="stat bg-warning/10 p-3">
                            <div class="stat-figure text-warning">
                                <x-heroicon-s-exclamation-triangle class="size-8" />
                            </div>
                            <div class="stat-title">{{ __('Pending') }}</div>
                            <div class="stat-value text-warning">{{ $unansweredQuestions }}</div>
                            <div class="stat-desc">{{ __('Awaiting response') }}</div>
                        </div>
                    </div>

                    @if ($totalQuestions > 0)
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-base-content/70 mb-1">
                                <span>{{ __('Response Rate') }}</span>
                                <span>{{ round(($answeredQuestions / $totalQuestions) * 100, 1) }}%</span>
                            </div>
                            <progress class="progress progress-success w-full" value="{{ $answeredQuestions }}"
                                max="{{ $totalQuestions }}"></progress>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Network Statistics -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h4 class="card-title text-info flex items-center gap-2">
                    <x-heroicon-s-building-office class="size-5" />
                    {{ __('Network & Organizations') }}
                </h4>

                <div class="card overflow-hidden grid grid-cols-1 md:grid-cols-2 mt-4">
                    <div class="stat bg-info/10 p-4">
                        <div class="stat-title text-info">{{ __('Issuers Met') }}</div>
                        <div class="stat-value text-info">{{ $issuersMetWith }}/{{ $totalIssuers }}</div>
                        <div class="stat-desc">{{ __('Conference coverage') }}</div>
                        @if ($totalIssuers > 0)
                            <div class="mt-2">
                                <progress class="progress progress-info w-full" value="{{ $issuersMetWith }}"
                                    max="{{ $totalIssuers }}"></progress>
                                <div class="text-xs text-info/70 mt-1">
                                    {{ round(($issuersMetWith / $totalIssuers) * 100, 1) }}%
                                    {{ __('of available issuers') }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="stat bg-secondary/10 p-4">
                        <div class="stat-title text-secondary">{{ __('Organizations') }}</div>
                        <div class="stat-value text-secondary">{{ $organizationsMetWith }}</div>
                        <div class="stat-desc">{{ __('Different companies met') }}</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-xl font-bold text-primary">
                <x-heroicon-s-calendar class="size-5" />
                {{ __('Next Meetings') }}
            </h3>
            @if ($upcomingMeetingsForDisplay->count() > 0)
                <a href="{{ route('investor.meetings.index') }}" class="btn btn-sm btn-outline btn-primary">
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
                                <th>{{ __('Issuer') }}</th>
                                <th>{{ __('Date & Time') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th class="text-center">{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($upcomingMeetingsForDisplay as $meeting)
                                @php
                                    $meetingInvestor = $meeting->meetingInvestors
                                        ->where('investor_id', Auth::id())
                                        ->first();
                                    $status = $meetingInvestor ? $meetingInvestor->status : null;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center space-x-3">
                                            <div>
                                                <div class="font-medium text-base-content">
                                                    {{ $meeting->issuer->first_name }}
                                                    {{ $meeting->issuer->name }}
                                                </div>
                                                <div class="text-sm text-base-content/60">
                                                    {{ $meeting->issuer->organization->name ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div>
                                                <div class="font-medium">
                                                    {{ $meeting->timeSlot->date->format('d M Y') }}
                                                </div>
                                                <div class="text-sm text-base-content/60">
                                                    {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                    {{ $meeting->timeSlot->end_time->format('H:i') }}
                                                </div>
                                            </div>
                                            @if ($meeting->timeSlot->start_time->diffInHours(now()) <= 2 && $meeting->timeSlot->start_time > now())
                                                <span class="badge badge-warning badge-xs">{{ __('Soon') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if ($meeting->room)
                                            <div class="font-medium">{{ $meeting->room->name }}</div>
                                            <div class="text-sm text-base-content/60">
                                                {{ $meeting->room->location }}</div>
                                        @else
                                            <span class="text-warning">{{ __('To be assigned') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($status)
                                            @switch($status->value)
                                                @case('pending')
                                                @case('invited')
                                                    <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                @break

                                                @case('accepted')
                                                @case('confirmed')
                                                    <span class="badge badge-success">{{ __('Confirmed') }}</span>
                                                @break

                                                @case('declined')
                                                    <span class="badge badge-error">{{ __('Declined') }}</span>
                                                @break

                                                @case('attended')
                                                    <span class="badge badge-info">{{ __('Attended') }}</span>
                                                @break

                                                @default
                                                    <span class="badge badge-neutral">{{ $status->value }}</span>
                                            @endswitch
                                        @else
                                            <span class="badge badge-ghost">{{ __('Unknown') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('investor.meetings.show', $meeting->id) }}"
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
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <x-heroicon-s-calendar class="size-12 text-primary/40" />
                    </div>
                    <h3 class="text-lg font-semibold text-base-content mb-2">{{ __('No Upcoming Meetings') }}</h3>
                    <p class="text-base-content/70 mb-6 max-w-md mx-auto">
                        {{ __('Start building your network by browsing available issuers and scheduling meetings with companies that interest you.') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('investor.issuers.index') }}" class="btn btn-primary">
                            <x-heroicon-s-building-office class="size-4" />
                            {{ __('Browse Issuers') }}
                        </a>
                        <a href="{{ route('investor.meetings.index') }}" class="btn btn-outline btn-primary">
                            <x-heroicon-s-calendar class="size-4" />
                            {{ __('View All Meetings') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
