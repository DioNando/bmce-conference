<x-app-layout>
    <!-- Breadcrumbs -->
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li>
                <a href="{{ route('investor.dashboard') }}" class="text-primary flex items-center gap-1">
                    {{ __('Dashboard') }}
                </a>
            </li>
            <li class="flex items-center gap-1">
                {{ __('Meetings') }}
            </li>
        </ul>
    </div>

    <!-- Header with stats -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">
            {{ __('My Meetings') }}
        </h1>
    </div>

    <div class="stats shadow bg-base-100 text-center mb-6">
        <div class="stat">
            <div class="stat-figure text-primary">
                <x-heroicon-s-calendar class="w-8 h-8" />
            </div>
            <div class="stat-title">{{ __('Total Meetings') }}</div>
            <div class="stat-value text-primary">{{ $meetings->count() }}</div>
        </div>
    </div>

    <!-- Meetings Tabs -->
    <div class="mb-6">
        <div class="card">
            <div class="card-body p-0 overflow-x-auto">
                <div role="tablist" class="tabs min-w-full rounded-box overflow-hidden">
                    <!-- Scheduled Tab -->
                    <input type="radio" name="meeting_tab" role="tab" class="tab text-primary"
                        aria-label="{{ __('Scheduled') }} ({{ $meetings->where('status', \App\Enums\MeetingStatus::SCHEDULED)->count() }})"
                        checked />
                    <div role="tabpanel" class="tab-content rounded-box bg-base-200 shadow-lg p-6">
                        @if ($meetings->where('status', \App\Enums\MeetingStatus::SCHEDULED)->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($meetings->where('status', \App\Enums\MeetingStatus::SCHEDULED) as $meeting)
                                    <div
                                        class="card card-bordered bg-base-100 shadow-md hover:border-primary transition-colors overflow-hidden">
                                        <div
                                            class="bg-primary/10 text-primary px-5 py-3 text-sm flex justify-between items-center">
                                            <h4 class="font-medium uppercase">{{ __('Scheduled Meeting') }}</h4>
                                            <span class="badge badge-success badge-sm flex items-center gap-1">
                                                <x-heroicon-s-clipboard-document-check class="w-3 h-3" />
                                                {{ $meeting->status->label() }}
                                            </span>
                                        </div>
                                        <div class="card-body p-5">
                                            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                                <div class="space-y-2">
                                                    <h4
                                                        class="text-lg font-semibold text-base-content flex items-center gap-1">
                                                        <x-heroicon-s-user-circle class="w-5 h-5" />
                                                        {{ __('Meeting with') }} {{ $meeting->issuer->first_name }}
                                                        {{ $meeting->issuer->name }}
                                                    </h4>
                                                    <p class="text-sm text-primary font-medium flex items-center gap-1">
                                                        <x-heroicon-s-building-office class="w-4 h-4" />
                                                        {{ $meeting->issuer->organization->name ?? __('No Organization') }}
                                                    </p>
                                                    <div class="space-y-2 mt-3">
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-calendar-days
                                                                class="w-4 h-4 text-primary/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Date:') }}</span>
                                                            {{ $meeting->timeSlot->date->format('d/m/Y') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-clock class="w-4 h-4 text-primary/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Time:') }}</span>
                                                            {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                            {{ $meeting->timeSlot->end_time->format('H:i') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-map-pin class="w-4 h-4 text-primary" />
                                                            <span class="font-medium">{{ __('Room:') }}</span>
                                                            @if ($meeting->room)
                                                                {{ $meeting->room->name }}
                                                            @else
                                                                <span
                                                                    class="text-base-content/50">{{ __('Virtual Meeting') }}</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1 text-xs">
                                                            <x-heroicon-s-chat-bubble-left-right
                                                                class="w-4 h-4 text-primary/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Questions:') }}</span>
                                                            {{ $meeting->questions->where('investor_id', auth()->id())->count() }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-3 md:items-end mt-2 md:mt-0">
                                                    @php
                                                        $investorStatus =
                                                            $meeting->investors->where('id', auth()->id())->first()
                                                                ->pivot->status ?? null;

                                                        $badgeClass = match ($investorStatus) {
                                                            \App\Enums\InvestorStatus::CONFIRMED->value
                                                                => 'badge-success',
                                                            \App\Enums\InvestorStatus::REFUSED->value => 'badge-error',
                                                            \App\Enums\InvestorStatus::PENDING->value
                                                                => 'badge-warning',
                                                            default => 'badge-neutral',
                                                        };
                                                    @endphp
                                                    @if ($investorStatus)
                                                        <span
                                                            class="badge {{ $badgeClass }} self-start md:self-auto flex items-center gap-1">
                                                            <x-heroicon-s-user class="w-3 h-3" />
                                                            {{ __('Your status:') }} {{ $investorStatus }}
                                                        </span>
                                                    @endif

                                                    <div class="mt-2">
                                                        <a href="{{ route('investor.meetings.show', $meeting->id) }}"
                                                            class="btn btn-primary btn-sm rounded-full flex items-center gap-1">
                                                            <x-heroicon-s-eye class="w-4 h-4" />
                                                            {{ __('View Details') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-base-content/40" />
                                <h3 class="mt-2 text-sm font-medium text-base-content">
                                    {{ __('No scheduled meetings') }}</h3>
                                <p class="mt-1 text-sm text-base-content/70">
                                    {{ __('You have no scheduled meetings at the moment.') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Confirmed Tab -->
                    <input type="radio" name="meeting_tab" role="tab" class="tab text-success"
                        aria-label="{{ __('Confirmed') }} ({{ $meetings->where('status', \App\Enums\MeetingStatus::CONFIRMED)->count() }})" />
                    <div role="tabpanel" class="tab-content rounded-box bg-base-200 shadow-lg p-6">
                        @if ($meetings->where('status', \App\Enums\MeetingStatus::CONFIRMED)->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($meetings->where('status', \App\Enums\MeetingStatus::CONFIRMED) as $meeting)
                                    <div
                                        class="card card-bordered bg-base-100 shadow-md hover:border-success transition-colors overflow-hidden">
                                        <div
                                            class="bg-success/10 text-success px-5 py-3 text-sm flex justify-between items-center">
                                            <h4 class="font-medium uppercase">{{ __('Confirmed Meeting') }}</h4>
                                            <span class="badge badge-info badge-sm flex items-center gap-1">
                                                <x-heroicon-s-check class="w-3 h-3" />
                                                {{ $meeting->status->label() }}
                                            </span>
                                        </div>
                                        <div class="card-body p-5">
                                            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                                <div class="space-y-2">
                                                    <h4
                                                        class="text-lg font-semibold text-base-content flex items-center gap-1">
                                                        <x-heroicon-s-user-circle class="w-5 h-5" />
                                                        {{ __('Meeting with') }} {{ $meeting->issuer->first_name }}
                                                        {{ $meeting->issuer->name }}
                                                    </h4>
                                                    <p class="text-sm text-success font-medium flex items-center gap-1">
                                                        <x-heroicon-s-building-office class="w-4 h-4" />
                                                        {{ $meeting->issuer->organization->name ?? __('No Organization') }}
                                                    </p>
                                                    <div class="space-y-2 mt-3">
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-calendar-days
                                                                class="w-4 h-4 text-success/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Date:') }}</span>
                                                            {{ $meeting->timeSlot->date->format('d/m/Y') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-clock class="w-4 h-4 text-success/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Time:') }}</span>
                                                            {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                            {{ $meeting->timeSlot->end_time->format('H:i') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-map-pin class="w-4 h-4 text-success/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Room:') }}</span>
                                                            @if ($meeting->room)
                                                                {{ $meeting->room->name }}
                                                            @else
                                                                <span
                                                                    class="text-base-content/50">{{ __('Virtual Meeting') }}</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1 text-xs">
                                                            <x-heroicon-s-chat-bubble-left-right
                                                                class="w-4 h-4 text-success/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Questions:') }}</span>
                                                            {{ $meeting->questions->where('investor_id', auth()->id())->count() }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-3 md:items-end mt-2 md:mt-0">
                                                    @php
                                                        $investorStatus =
                                                            $meeting->investors->where('id', auth()->id())->first()
                                                                ->pivot->status ?? null;

                                                        $badgeClass = match ($investorStatus) {
                                                            \App\Enums\InvestorStatus::CONFIRMED->value
                                                                => 'badge-success',
                                                            \App\Enums\InvestorStatus::REFUSED->value => 'badge-error',
                                                            \App\Enums\InvestorStatus::PENDING->value
                                                                => 'badge-warning',
                                                            default => 'badge-neutral',
                                                        };
                                                    @endphp
                                                    @if ($investorStatus)
                                                        <span
                                                            class="badge {{ $badgeClass }} self-start md:self-auto flex items-center gap-1">
                                                            <x-heroicon-s-user class="w-3 h-3" />
                                                            {{ __('Your status:') }} {{ $investorStatus }}
                                                        </span>
                                                    @endif

                                                    <div class="mt-2">
                                                        <a href="{{ route('investor.meetings.show', $meeting->id) }}"
                                                            class="btn btn-primary btn-sm rounded-full flex items-center gap-1">
                                                            <x-heroicon-s-eye class="w-4 h-4" />
                                                            {{ __('View Details') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <x-heroicon-o-check-circle class="mx-auto h-12 w-12 text-base-content/40" />
                                <h3 class="mt-2 text-sm font-medium text-base-content">
                                    {{ __('No confirmed meetings') }}</h3>
                                <p class="mt-1 text-sm text-base-content/70">
                                    {{ __('You have no confirmed meetings at the moment.') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Pending Tab -->
                    <input type="radio" name="meeting_tab" role="tab" class="tab text-warning"
                        aria-label="{{ __('Pending') }} ({{ $meetings->where('status', \App\Enums\MeetingStatus::PENDING)->count() }})" />
                    <div role="tabpanel" class="tab-content rounded-box bg-base-200 shadow-lg p-6">
                        @if ($meetings->where('status', \App\Enums\MeetingStatus::PENDING)->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($meetings->where('status', \App\Enums\MeetingStatus::PENDING) as $meeting)
                                    <div
                                        class="card card-bordered bg-base-100 shadow-md hover:border-warning transition-colors overflow-hidden">
                                        <div
                                            class="bg-warning/10 text-warning px-5 py-3 text-sm flex justify-between items-center">
                                            <h4 class="font-medium uppercase">{{ __('Pending Meeting') }}</h4>
                                            <span class="badge badge-warning badge-sm flex items-center gap-1">
                                                <x-heroicon-s-clock class="w-3 h-3" />
                                                {{ $meeting->status->label() }}
                                            </span>
                                        </div>
                                        <div class="card-body p-5">
                                            <div
                                                class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                                <div class="space-y-2">
                                                    <h4
                                                        class="text-lg font-semibold text-base-content flex items-center gap-1">
                                                        <x-heroicon-s-user-circle class="w-5 h-5" />
                                                        {{ __('Meeting with') }} {{ $meeting->issuer->first_name }}
                                                        {{ $meeting->issuer->name }}
                                                    </h4>
                                                    <p
                                                        class="text-sm text-warning font-medium flex items-center gap-1">
                                                        <x-heroicon-s-building-office class="w-4 h-4" />
                                                        {{ $meeting->issuer->organization->name ?? __('No Organization') }}
                                                    </p>
                                                    <div class="space-y-2 mt-3">
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-calendar-days
                                                                class="w-4 h-4 text-warning/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Date:') }}</span>
                                                            {{ $meeting->timeSlot->date->format('d/m/Y') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-clock class="w-4 h-4 text-warning/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Time:') }}</span>
                                                            {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                            {{ $meeting->timeSlot->end_time->format('H:i') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-map-pin class="w-4 h-4 text-warning/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Room:') }}</span>
                                                            @if ($meeting->room)
                                                                {{ $meeting->room->name }}
                                                            @else
                                                                <span
                                                                    class="text-base-content/50">{{ __('Virtual Meeting') }}</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-3 md:items-end mt-2 md:mt-0">
                                                    @php
                                                        $investorStatus =
                                                            $meeting->investors->where('id', auth()->id())->first()
                                                                ->pivot->status ?? null;

                                                        $badgeClass = match ($investorStatus) {
                                                            \App\Enums\InvestorStatus::CONFIRMED->value
                                                                => 'badge-success',
                                                            \App\Enums\InvestorStatus::REFUSED->value => 'badge-error',
                                                            \App\Enums\InvestorStatus::PENDING->value
                                                                => 'badge-warning',
                                                            default => 'badge-neutral',
                                                        };
                                                    @endphp
                                                    @if ($investorStatus)
                                                        <span
                                                            class="badge {{ $badgeClass }} self-start md:self-auto flex items-center gap-1">
                                                            <x-heroicon-s-user class="w-3 h-3" />
                                                            {{ __('Your status:') }} {{ $investorStatus }}
                                                        </span>
                                                    @endif

                                                    <div class="mt-2">
                                                        <a href="{{ route('investor.meetings.show', $meeting->id) }}"
                                                            class="btn btn-primary btn-sm rounded-full flex items-center gap-1">
                                                            <x-heroicon-s-eye class="w-4 h-4" />
                                                            {{ __('View Details') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <x-heroicon-o-clock class="mx-auto h-12 w-12 text-base-content/40" />
                                <h3 class="mt-2 text-sm font-medium text-base-content">{{ __('No pending meetings') }}
                                </h3>
                                <p class="mt-1 text-sm text-base-content/70">
                                    {{ __('You have no pending meeting requests.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Completed Tab -->
                    <input type="radio" name="meeting_tab" role="tab" class="tab text-neutral"
                        aria-label="{{ __('Completed') }} ({{ $meetings->where('status', \App\Enums\MeetingStatus::COMPLETED)->count() }})" />
                    <div role="tabpanel" class="tab-content rounded-box bg-base-200 shadow-lg p-6">
                        @if ($meetings->where('status', \App\Enums\MeetingStatus::COMPLETED)->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($meetings->where('status', \App\Enums\MeetingStatus::COMPLETED) as $meeting)
                                    <div
                                        class="card card-bordered bg-base-100 shadow-md hover:border-neutral transition-colors overflow-hidden">
                                        <div
                                            class="bg-neutral/10 text-neutral px-5 py-3 text-sm flex justify-between items-center">
                                            <h4 class="font-medium uppercase">{{ __('Completed Meeting') }}</h4>
                                            <span class="badge badge-neutral badge-sm flex items-center gap-1">
                                                <x-heroicon-s-check-badge class="w-3 h-3" />
                                                {{ $meeting->status->label() }}
                                            </span>
                                        </div>
                                        <div class="card-body p-5">
                                            <div
                                                class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                                <div class="space-y-2">
                                                    <h4
                                                        class="text-lg font-semibold text-base-content flex items-center gap-1">
                                                        <x-heroicon-s-user-circle class="w-5 h-5" />
                                                        {{ __('Meeting with') }} {{ $meeting->issuer->first_name }}
                                                        {{ $meeting->issuer->name }}
                                                    </h4>
                                                    <p
                                                        class="text-sm text-neutral font-medium flex items-center gap-1">
                                                        <x-heroicon-s-building-office class="w-4 h-4" />
                                                        {{ $meeting->issuer->organization->name ?? __('No Organization') }}
                                                    </p>
                                                    <div class="space-y-2 mt-3">
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-calendar-days
                                                                class="w-4 h-4 text-neutral/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Date:') }}</span>
                                                            {{ $meeting->timeSlot->date->format('d/m/Y') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-clock class="w-4 h-4 text-neutral/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Time:') }}</span>
                                                            {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                            {{ $meeting->timeSlot->end_time->format('H:i') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-map-pin class="w-4 h-4 text-neutral/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Room:') }}</span>
                                                            @if ($meeting->room)
                                                                {{ $meeting->room->name }}
                                                            @else
                                                                <span
                                                                    class="text-base-content/50">{{ __('Virtual Meeting') }}</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1 text-xs">
                                                            <x-heroicon-s-chat-bubble-left-right
                                                                class="w-4 h-4 text-neutral/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Questions:') }}</span>
                                                            {{ $meeting->questions->where('investor_id', auth()->id())->count() }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-3 md:items-end mt-2 md:mt-0">
                                                    @php
                                                        $investorStatus =
                                                            $meeting->investors->where('id', auth()->id())->first()
                                                                ->pivot->status ?? null;

                                                        $badgeClass = match ($investorStatus) {
                                                            \App\Enums\InvestorStatus::CONFIRMED->value
                                                                => 'badge-success',
                                                            \App\Enums\InvestorStatus::REFUSED->value => 'badge-error',
                                                            \App\Enums\InvestorStatus::PENDING->value
                                                                => 'badge-warning',
                                                            default => 'badge-neutral',
                                                        };
                                                    @endphp
                                                    @if ($investorStatus)
                                                        <span
                                                            class="badge {{ $badgeClass }} self-start md:self-auto flex items-center gap-1">
                                                            <x-heroicon-s-user class="w-3 h-3" />
                                                            {{ __('Your status:') }} {{ $investorStatus }}
                                                        </span>
                                                    @endif

                                                    <div class="mt-2">
                                                        <a href="{{ route('investor.meetings.show', $meeting->id) }}"
                                                            class="btn btn-neutral btn-sm rounded-full flex items-center gap-1">
                                                            <x-heroicon-s-eye class="w-4 h-4" />
                                                            {{ __('View Details') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <x-heroicon-o-document-check class="mx-auto h-12 w-12 text-base-content/40" />
                                <h3 class="mt-2 text-sm font-medium text-base-content">
                                    {{ __('No completed meetings') }}</h3>
                                <p class="mt-1 text-sm text-base-content/70">
                                    {{ __('You have no completed meetings yet.') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Cancelled Tab -->
                    <input type="radio" name="meeting_tab" role="tab" class="tab text-error"
                        aria-label="{{ __('Cancelled') }} ({{ $meetings->where('status', \App\Enums\MeetingStatus::CANCELLED)->count() }})" />
                    <div role="tabpanel" class="tab-content rounded-box bg-base-200 shadow-lg p-6">
                        @if ($meetings->where('status', \App\Enums\MeetingStatus::CANCELLED)->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($meetings->where('status', \App\Enums\MeetingStatus::CANCELLED) as $meeting)
                                    <div
                                        class="card card-bordered bg-base-100 shadow-md hover:border-error transition-colors overflow-hidden">
                                        <div
                                            class="bg-error/10 text-error px-5 py-3 text-sm flex justify-between items-center">
                                            <h4 class="font-medium uppercase">{{ __('Cancelled Meeting') }}</h4>
                                            <span class="badge badge-error badge-sm flex items-center gap-1">
                                                <x-heroicon-s-x-circle class="w-3 h-3" />
                                                {{ $meeting->status->label() }}
                                            </span>
                                        </div>
                                        <div class="card-body p-5">
                                            <div
                                                class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                                <div class="space-y-2">
                                                    <h4
                                                        class="text-lg font-semibold text-base-content flex items-center gap-1">
                                                        <x-heroicon-s-user-circle class="w-5 h-5" />
                                                        {{ __('Meeting with') }} {{ $meeting->issuer->first_name }}
                                                        {{ $meeting->issuer->name }}
                                                    </h4>
                                                    <p class="text-sm text-error font-medium flex items-center gap-1">
                                                        <x-heroicon-s-building-office class="w-4 h-4" />
                                                        {{ $meeting->issuer->organization->name ?? __('No Organization') }}
                                                    </p>
                                                    <div class="space-y-2 mt-3">
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-calendar-days
                                                                class="w-4 h-4 text-error/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Date:') }}</span>
                                                            {{ $meeting->timeSlot->date->format('d/m/Y') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-clock class="w-4 h-4 text-error/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Time:') }}</span>
                                                            {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                            {{ $meeting->timeSlot->end_time->format('H:i') }}
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1">
                                                            <x-heroicon-s-map-pin class="w-4 h-4 text-error/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Room:') }}</span>
                                                            @if ($meeting->room)
                                                                {{ $meeting->room->name }}
                                                            @else
                                                                <span
                                                                    class="text-base-content/50">{{ __('Virtual Meeting') }}</span>
                                                            @endif
                                                        </p>
                                                        <p class="text-base-content flex items-center gap-1 text-xs">
                                                            <x-heroicon-s-chat-bubble-left-right
                                                                class="w-4 h-4 text-error/70" />
                                                            <span
                                                                class="font-medium text-base-content/80">{{ __('Questions:') }}</span>
                                                            {{ $meeting->questions->where('investor_id', auth()->id())->count() }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col gap-3 md:items-end mt-2 md:mt-0">
                                                    @php
                                                        $investorStatus =
                                                            $meeting->investors->where('id', auth()->id())->first()
                                                                ->pivot->status ?? null;

                                                        $badgeClass = match ($investorStatus) {
                                                            \App\Enums\InvestorStatus::CONFIRMED->value
                                                                => 'badge-success',
                                                            \App\Enums\InvestorStatus::REFUSED->value => 'badge-error',
                                                            \App\Enums\InvestorStatus::PENDING->value
                                                                => 'badge-warning',
                                                            default => 'badge-neutral',
                                                        };
                                                    @endphp
                                                    @if ($investorStatus)
                                                        <span
                                                            class="badge {{ $badgeClass }} self-start md:self-auto flex items-center gap-1">
                                                            <x-heroicon-s-user class="w-3 h-3" />
                                                            {{ __('Your status:') }} {{ $investorStatus }}
                                                        </span>
                                                    @endif

                                                    <div class="mt-2">
                                                        <a href="{{ route('investor.meetings.show', $meeting->id) }}"
                                                            class="btn btn-error btn-sm rounded-full flex items-center gap-1">
                                                            <x-heroicon-s-eye class="w-4 h-4" />
                                                            {{ __('View Details') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <x-heroicon-o-x-circle class="mx-auto h-12 w-12 text-base-content/40" />
                                <h3 class="mt-2 text-sm font-medium text-base-content">
                                    {{ __('No cancelled meetings') }}</h3>
                                <p class="mt-1 text-sm text-base-content/70">
                                    {{ __('You have no cancelled meetings yet.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
