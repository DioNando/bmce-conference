@php
    $INVESTOR_STATUS = \App\Enums\InvestorStatus::class;
    $MEETING_STATUS = \App\Enums\MeetingStatus::class;

    // Calculate investor status statistics
    $totalInvestors = $meeting->investors->count();
    $confirmedCount = $meeting->meetingInvestors->where('status', $INVESTOR_STATUS::CONFIRMED)->count();
    $pendingCount = $meeting->meetingInvestors->where('status', $INVESTOR_STATUS::PENDING)->count();
    $refusedCount = $meeting->meetingInvestors->where('status', $INVESTOR_STATUS::REFUSED)->count();
    $attendedCount = $meeting->meetingInvestors->where('status', $INVESTOR_STATUS::ATTENDED)->count();
    $absentCount = $meeting->meetingInvestors->where('status', $INVESTOR_STATUS::ABSENT)->count();

    // Calculate question statistics
    $totalQuestions = $meeting->questions->count();
    $answeredQuestions = $meeting->questions->where('is_answered', true)->count();
    $answerRate = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;

    // Calculate meeting time status
    $now = now();
    $meetingDate = $meeting->timeSlot->date;
    $isPast = $meetingDate->isPast();
    $timeRemaining = $now->diffForHumans($meetingDate, ['parts' => 2]);

    // Calculate attendance rate
    $attendanceRate = $totalInvestors > 0 ? round(($attendedCount / $totalInvestors) * 100) : 0;
@endphp

<x-app-layout>
    <section class="space-y-6">
        <div class="breadcrumbs text-sm mb-4">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
                <li><a href="{{ route('admin.meetings.index') }}" class="text-primary">{{ __('Meetings') }}</a></li>
                <li>{{ __('Meeting Details') }}</li>
            </ul>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary">
                {{ __('Meeting #:id', ['id' => $meeting->id]) }}
            </h1>
        </div>



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

        <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-4 space-y-6 grid grid-cols-1 lg:grid-cols-2">
                <!-- Meeting Details -->
                <div class="lg:col-span-2 card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Meeting Information') }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Status') }}</div>
                                <div class="mt-1">
                                    @switch($meeting->status->value)
                                        @case($MEETING_STATUS::SCHEDULED->value)
                                            <span class="badge badge-info">{{ __($meeting->status->label()) }}</span>
                                        @break

                                        @case($MEETING_STATUS::COMPLETED->value)
                                            <span class="badge badge-success">{{ __($meeting->status->label()) }}</span>
                                        @break

                                        @case($MEETING_STATUS::CANCELLED->value)
                                            <span class="badge badge-error">{{ __($meeting->status->label()) }}</span>
                                        @break

                                        @case($MEETING_STATUS::PENDING->value)
                                            <span class="badge badge-warning">{{ __($meeting->status->label()) }}</span>
                                        @break

                                        @case($MEETING_STATUS::CONFIRMED->value)
                                            <span class="badge badge-success">{{ __($meeting->status->label()) }}</span>
                                        @break

                                        @case($MEETING_STATUS::DECLINED->value)
                                            <span class="badge badge-error">{{ __($meeting->status->label()) }}</span>
                                        @break

                                        @default
                                            <span class="badge badge-neutral">{{ __($meeting->status->label()) }}</span>
                                    @endswitch
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">
                                    {{ __('Meeting Type') }}</div>
                                <div class="mt-1 text-sm">
                                    @if ($meeting->is_one_on_one)
                                        <span>{{ __('One-on-One (individual)') }}</span>
                                    @else
                                        <span>{{ __('Group') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">
                                    {{ __('Date & Time') }}</div>
                                <div class="mt-1 text-sm">
                                    {{ $meeting->timeSlot->date->format('d/m/Y') }} -
                                    {{ $meeting->timeSlot->start_time->format('H:i') }}
                                    {{ $meeting->timeSlot->end_time->format('H:i') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Room') }}</div>
                                <div class="mt-1 text-sm">
                                    @if ($meeting->room)
                                        {{ $meeting->room->name }}
                                        <span class="text-xs text-base-content/70">(Capacity:
                                            {{ $meeting->room->capacity }})</span>
                                    @else
                                        <span class="text-base-content/50">{{ __('No Room Assigned') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="text-sm font-medium text-base-content/70">{{ __('Notes') }}</div>
                            <div class="mt-1 text-sm">
                                {{ $meeting->notes ?? __('No notes') }}
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-base-200">
                            <div class="text-sm font-medium text-base-content/70">
                                {{ __('Additional Information') }}</div>
                            <div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div><span class="font-medium">{{ __('Created by:') }}</span>
                                    {{ $meeting->createdBy->name . ' ' . $meeting->createdBy->first_name ?? __('Unknown') }}
                                </div>
                                <div><span class="font-medium">{{ __('Created on:') }}</span>
                                    {{ $meeting->created_at->format('d/m/Y H:i') }}</div>
                                <div><span class="font-medium">{{ __('Last updated by:') }}</span>
                                    {{ $meeting->updatedBy->name . ' ' . $meeting->updatedBy->first_name ?? __('Unknown') }}
                                </div>
                                <div><span class="font-medium">{{ __('Last updated on:') }}</span>
                                    {{ $meeting->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meeting Statistics -->
                <div class="lg:col-span-2 card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Meeting Statistics') }}</h2>

                        <!-- Meeting Time Status -->
                        <div class="mb-4">
                            <div class="text-sm font-medium text-base-content/70 mb-2">{{ __('Time Status') }}</div>
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-base-200 rounded-box p-6">
                                    @if ($isPast)
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium">{{ __('Meeting was') }}
                                                {{ $timeRemaining }}</span>
                                            <span class="badge badge-neutral">{{ __('Past') }}</span>
                                        </div>
                                        <div class="relative h-2 w-full bg-base-300 rounded-full overflow-hidden">
                                            <div class="absolute top-0 left-0 h-full bg-accent w-full"></div>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium">{{ __('Meeting is') }}
                                                {{ $timeRemaining }}</span>
                                            <span class="badge badge-info">{{ __('Upcoming') }}</span>
                                        </div>
                                        <div class="relative h-2 w-full bg-base-300 rounded-full overflow-hidden">
                                            @php
                                                $now = now();
                                                $totalDays = max(
                                                    1,
                                                    $now->diffInDays($meeting->created_at, false) +
                                                        $meeting->created_at->diffInDays($meetingDate, false),
                                                );
                                                $elapsedDays = $now->diffInDays($meeting->created_at, false);
                                                $progressPercent = min(
                                                    100,
                                                    max(0, round(($elapsedDays / $totalDays) * 100)),
                                                );
                                            @endphp
                                            <div class="absolute top-0 left-0 h-full bg-primary"
                                                style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        <div class="flex justify-between text-xs text-base-content/70 mt-1">
                                            <span>{{ __('Created') }}:
                                                {{ $meeting->created_at->format('d/m/Y') }}</span>
                                            <span>{{ __('Meeting Date') }}: {{ $meetingDate->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Investor Status Stats -->
                        <div>
                            <div class="text-sm font-medium text-base-content/70 mb-2">{{ __('Investor Status') }}
                            </div>
                            <div class="bg-base-200 p-6 rounded-box">
                                <div class="text-sm font-medium mb-2">{{ __('Summary') }}</div>

                                <div class="flex justify-between text-xs mb-1">
                                    <span>{{ __('Confirmed') }}: {{ $confirmedCount }}/{{ $totalInvestors }}</span>
                                    <span>{{ $totalInvestors > 0 ? round(($confirmedCount / $totalInvestors) * 100) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-base-300 rounded-full h-1.5 mb-2">
                                    <div class="bg-info h-1.5 rounded-full"
                                        style="width: {{ $totalInvestors > 0 ? round(($confirmedCount / $totalInvestors) * 100) : 0 }}%">
                                    </div>
                                </div>

                                <div class="flex justify-between text-xs mb-1">
                                    <span>{{ __('Pending') }}: {{ $pendingCount }}/{{ $totalInvestors }}</span>
                                    <span>{{ $totalInvestors > 0 ? round(($pendingCount / $totalInvestors) * 100) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-base-300 rounded-full h-1.5 mb-2">
                                    <div class="bg-warning h-1.5 rounded-full"
                                        style="width: {{ $totalInvestors > 0 ? round(($pendingCount / $totalInvestors) * 100) : 0 }}%">
                                    </div>
                                </div>

                                <div class="flex justify-between text-xs mb-1">
                                    <span>{{ __('Refused') }}: {{ $refusedCount }}/{{ $totalInvestors }}</span>
                                    <span>{{ $totalInvestors > 0 ? round(($refusedCount / $totalInvestors) * 100) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-base-300 rounded-full h-1.5 mb-2">
                                    <div class="bg-error h-1.5 rounded-full"
                                        style="width: {{ $totalInvestors > 0 ? round(($refusedCount / $totalInvestors) * 100) : 0 }}%">
                                    </div>
                                </div>

                                <div class="flex justify-between text-xs mb-1">
                                    <span>{{ __('Attended') }}: {{ $attendedCount }}/{{ $totalInvestors }}</span>
                                    <span>{{ $attendanceRate }}%</span>
                                </div>
                                <div class="w-full bg-base-300 rounded-full h-1.5 mb-2">
                                    <div class="bg-success h-1.5 rounded-full"
                                        style="width: {{ $attendanceRate }}%">
                                    </div>
                                </div>

                                <div class="flex justify-between text-xs mb-1">
                                    <span>{{ __('Absent') }}: {{ $absentCount }}/{{ $totalInvestors }}</span>
                                    <span>{{ $totalInvestors > 0 ? round(($absentCount / $totalInvestors) * 100) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-base-300 rounded-full h-1.5 mb-2">
                                    <div class="bg-neutral h-1.5 rounded-full"
                                        style="width: {{ $totalInvestors > 0 ? round(($absentCount / $totalInvestors) * 100) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions -->
                <div class="lg:col-span-2 card card-bordered bg-base-200 shadow-lg">
                    <div class="card-body">
                        <div class="flex gap-2 justify-between items-center mb-3">
                            <h2 class="card-title text-primary">{{ __('Investor Questions') }}</h2>
                            <div class="flex items-center gap-2">
                                <span
                                    class="badge badge-primary">{{ $meeting->questions->count() . ' ' . __('question(s)') }}</span>
                                @if ($meeting->questions->count() > 0)
                                    <span
                                        class="badge {{ $answerRate >= 75 ? 'badge-success' : ($answerRate >= 50 ? 'badge-info' : 'badge-warning') }}">
                                        {{ $answerRate }}% {{ __('answered') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($meeting->questions->count() > 0)
                            <!-- Questions Filter -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <button class="btn btn-sm rounded-full filter-btn active" data-filter="all">
                                    {{ __('All') }} ({{ $meeting->questions->count() }})
                                </button>
                                <button class="btn btn-sm rounded-full filter-btn" data-filter="answered">
                                    {{ __('Answered') }} ({{ $answeredQuestions }})
                                </button>
                                <button class="btn btn-sm rounded-full filter-btn" data-filter="pending">
                                    {{ __('Pending') }} ({{ $totalQuestions - $answeredQuestions }})
                                </button>
                            </div>
                        @endif
                        <div class="flex flex-col gap-4">
                            @forelse ($meeting->questions as $question)
                                <div
                                    class="question-item {{ $question->is_answered ? 'answered' : 'pending' }} card shadow-md bg-base-100 rounded-box">
                                    <div class="card-body">
                                        <div class="flex justify-between mb-2">
                                            <div class="flex items-center">
                                                <span
                                                    class="font-medium">{{ $question->user->name . ' ' . $question->user->first_name }}</span>
                                                <span class="mx-2 text-base-content/70">&#8226;</span>
                                                <span
                                                    class="text-sm text-base-content/70">{{ $question->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if ($question->is_answered)
                                                    <span class="badge badge-success">{{ __('Answered') }}</span>
                                                    @if ($question->answered_at)
                                                        <span
                                                            class="text-xs text-base-content/70">{{ $question->answered_at->diffForHumans() }}</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                    <span
                                                        class="text-xs text-base-content/70">{{ $question->created_at->diffForHumans() }}</span>
                                                @endif

                                                <!-- Delete Question Button -->
                                                <button type="button"
                                                    class="btn btn-square btn-xs btn-error btn-soft"
                                                    @click="$dispatch('open-modal', 'delete-question-{{ $question->id }}')">
                                                    <x-heroicon-s-trash class="size-4" />
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="bg-base-200 p-3 rounded-lg">
                                                <p>{{ $question->question }}</p>
                                            </div>
                                            @if ($question->response)
                                                <div class="mt-3">
                                                    <div class="flex items-center mb-2">
                                                        <span
                                                            class="text-sm font-medium text-base-content/70">{{ __('Response') }}
                                                        </span>
                                                        <span class="mx-2 text-base-content/70">&#8226;</span>
                                                        <span
                                                            class="text-xs text-base-content/70">{{ $question->updated_at->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                    <p class="bg-primary text-primary-content p-4 rounded-box w-fit">
                                                        {{ $question->response }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- @if (!$loop->last)
                                <div class="my-4 border-b border-base-200"></div>
                            @endif --}}

                                <!-- Question Deletion Modal -->
                                <x-modal name="delete-question-{{ $question->id }}" :show="false" focusable>
                                    <div class="p-6">
                                        <h3 class="text-lg font-medium text-error">
                                            {{ __('Confirm Question Deletion') }}
                                        </h3>

                                        <p class="mt-3 text-sm text-base-content/70">
                                            {{ __('Are you sure you want to delete this question?') }}
                                            {{ __('This action cannot be undone.') }}
                                        </p>

                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button" class="btn btn-ghost"
                                                x-on:click="$dispatch('close')">
                                                {{ __('Cancel') }}
                                            </button>

                                            <form action="{{ route('admin.questions.destroy', $question) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-error">
                                                    {{ __('Delete Question') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </x-modal>
                            @empty
                                <p class="text-base-content/70 italic">
                                    {{ __('No questions for this meeting.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar with Participants -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Actions Panel -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Actions') }}</h2>
                        <div class="space-y-3 mt-4">
                            <a href="{{ route('admin.attendance.scanner', $meeting) }}"
                                class="btn btn-warning w-full">
                                {{ __('Scan QR Code') }}
                            </a>
                            <a href="{{ route('admin.meetings.investors', $meeting) }}"
                                class="btn btn-accent w-full">
                                {{ __('See Investors') }}
                            </a>
                            <a href="{{ route('admin.meetings.edit', $meeting) }}" class="btn btn-primary w-full">
                                {{ __('Edit Meeting') }}
                            </a>
                            <button type="button" class="btn btn-error w-full"
                                @click="$dispatch('open-modal', 'delete-meeting')">
                                {{ __('Delete Meeting') }}
                            </button>
                            <a href="{{ route('admin.meetings.index') }}" class="btn btn-outline w-full">
                                {{ __('Back to Meetings') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Issuer -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Issuer') }}</h2>

                        <div class="flex items-center">
                            <div>
                                <a href="{{ route('admin.users.show', $meeting->issuer) }}"
                                    class="text-lg font-medium text-primary link">
                                    {{ $meeting->issuer->name . ' ' . $meeting->issuer->first_name }}</a>
                                @if ($meeting->issuer->organization)
                                    <div class="text-sm text-base-content/70">
                                        {{ $meeting->issuer->organization->name }}</div>
                                @endif
                                <div class="text-sm text-base-content/70">{{ $meeting->issuer->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investors -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex gap-2 justify-between items-center mb-3">
                            <h2 class="card-title text-primary">{{ __('Investors') }}</h2>
                            <span
                                class="badge badge-primary">{{ $meeting->investors->count() . ' ' . __('participant(s)') }}</span>
                        </div>



                        <div>
                            @forelse ($meeting->investors as $investor)
                                <div>
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <a href="{{ route('admin.users.show', $investor) }}" class="text-sm font-medium underline text-primary">
                                                {{ $investor->name . ' ' . $investor->first_name }}</a>
                                            @if ($investor->organization)
                                                <div class="text-xs text-base-content/70">
                                                    {{ $investor->organization->name }}</div>
                                            @endif
                                            <div class="text-xs text-base-content/70">
                                                {{ $investor->email }}</div>
                                            <div class="mt-1">
                                                @php
                                                    $meetingInvestor = $meeting->meetingInvestors
                                                        ->where('investor_id', $investor->id)
                                                        ->first();
                                                    $status = $meetingInvestor
                                                        ? $meetingInvestor->status->value
                                                        : 'unknown';
                                                @endphp
                                                @switch($status)
                                                    @case($INVESTOR_STATUS::PENDING->value)
                                                        <span
                                                            class="badge badge-{{ $INVESTOR_STATUS::PENDING->color() }}">{{ __($INVESTOR_STATUS::PENDING->label()) }}</span>
                                                    @break

                                                    @case($INVESTOR_STATUS::CONFIRMED->value)
                                                        <span
                                                            class="badge badge-{{ $INVESTOR_STATUS::CONFIRMED->color() }}">{{ __($INVESTOR_STATUS::CONFIRMED->label()) }}</span>
                                                    @break

                                                    @case($INVESTOR_STATUS::REFUSED->value)
                                                        <span
                                                            class="badge badge-{{ $INVESTOR_STATUS::REFUSED->color() }}">{{ __($INVESTOR_STATUS::REFUSED->label()) }}</span>
                                                    @break

                                                    @case($INVESTOR_STATUS::ATTENDED->value)
                                                        <span
                                                            class="badge badge-{{ $INVESTOR_STATUS::ATTENDED->color() }}">{{ __($INVESTOR_STATUS::ATTENDED->label()) }}</span>
                                                    @break

                                                    @case($INVESTOR_STATUS::ABSENT->value)
                                                        <span
                                                            class="badge badge-{{ $INVESTOR_STATUS::ABSENT->color() }}">{{ __($INVESTOR_STATUS::ABSENT->label()) }}</span>
                                                    @break

                                                    @default
                                                        <span class="badge badge-neutral">{{ __('Unknown') }}</span>
                                                @endswitch
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            @if ($status !== $INVESTOR_STATUS::ATTENDED->value)
                                                <form
                                                    action="{{ route('admin.meetings.update-investor-status', ['meeting' => $meeting->id, 'investor' => $investor->id]) }}"
                                                    method="POST" class="status-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status"
                                                        class="select select-bordered select-sm status-select"
                                                        onchange="this.form.submit()">
                                                        <option value="{{ $INVESTOR_STATUS::CONFIRMED->value }}"
                                                            {{ $status === $INVESTOR_STATUS::CONFIRMED->value ? 'selected' : '' }}>
                                                            {{ __($INVESTOR_STATUS::CONFIRMED->label()) }}
                                                        </option>
                                                        <option value="{{ $INVESTOR_STATUS::PENDING->value }}"
                                                            {{ $status === $INVESTOR_STATUS::PENDING->value ? 'selected' : '' }}>
                                                            {{ __($INVESTOR_STATUS::PENDING->label()) }}</option>
                                                        <option value="{{ $INVESTOR_STATUS::REFUSED->value }}"
                                                            {{ $status === $INVESTOR_STATUS::REFUSED->value ? 'selected' : '' }}>
                                                            {{ __($INVESTOR_STATUS::REFUSED->label()) }}</option>
                                                        <option value="{{ $INVESTOR_STATUS::ABSENT->value }}"
                                                            {{ $status === $INVESTOR_STATUS::ABSENT->value ? 'selected' : '' }}>
                                                            {{ __($INVESTOR_STATUS::ABSENT->label()) }}</option>
                                                        {{-- <option value="{{ $INVESTOR_STATUS::ATTENDED->value }}"
                                                                 {{ $status === $INVESTOR_STATUS::ATTENDED->value ? 'selected' : '' }}>
                                                                 {{ __($INVESTOR_STATUS::ATTENDED->label()) }}</option> --}}
                                                    </select>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <div class="my-4 border-b border-base-200"></div>
                                @endif
                                @empty
                                    <p class="text-base-content/70 italic">
                                        {{ __('No investors have been added to this meeting.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Meeting Deletion Modal -->
        <x-modal name="delete-meeting" :show="false" focusable>
            <div class="p-6">
                <h3 class="text-lg font-medium text-error">
                    {{ __('Confirm Meeting Deletion') }}
                </h3>

                <p class="mt-3 text-sm text-base-content/70">
                    {{ __('Are you sure you want to delete this meeting?') }}
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

        <script>
            // Animation lors de la soumission du formulaire
            document.addEventListener('DOMContentLoaded', function() {
                const statusSelects = document.querySelectorAll('.status-select');
                statusSelects.forEach(select => {
                    select.addEventListener('change', function() {
                        this.classList.add('animate-pulse');
                        setTimeout(() => {
                            this.classList.remove('animate-pulse');
                        }, 1500);
                    });
                });

                // Filtrage des questions (style actif)
                const filterBtns = document.querySelectorAll('.filter-btn');
                filterBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Retirer la classe active de tous les boutons
                        filterBtns.forEach(b => {
                            b.classList.remove('active');
                            b.classList.remove('btn-primary');
                            b.classList.add('btn-soft');
                        });

                        // Ajouter la classe active au bouton cliqué
                        this.classList.add('active');
                        this.classList.add('btn-primary');
                        this.classList.remove('btn-soft');

                        const filter = this.getAttribute('data-filter');

                        // Filtrer les questions
                        const questionItems = document.querySelectorAll('.question-item');
                        questionItems.forEach(item => {
                            if (filter === 'all') {
                                item.style.display = 'block';
                            } else if (filter === 'answered' && item.classList.contains(
                                    'answered')) {
                                item.style.display = 'block';
                            } else if (filter === 'pending' && item.classList.contains(
                                    'pending')) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    });
                });

                // Initialiser le filtre sur 'all' par défaut
                const allFilterBtn = document.querySelector('.filter-btn[data-filter="all"]');
                if (allFilterBtn) {
                    allFilterBtn.classList.add('btn-primary');
                    allFilterBtn.classList.remove('btn-soft');
                }
            });
        </script>
    </x-app-layout>
