<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('investor.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('investor.meetings.index') }}" class="text-primary">{{ __('Meetings') }}</a></li>
            <li>{{ __('Meeting Details') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-primary">
                {{ __('Meeting on :date', ['date' => $meeting->timeSlot->date->format('F j, Y')]) }}
            </h1>
        </div>

        @if (session('info'))
            <div role="alert" class="alert alert-warning">
                <span>{{ session('info') }}</span>
            </div>
        @endif

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Meeting Details -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Meeting Information') }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Status') }}</div>
                                <div class="mt-1">
                                    @php
                                        $badgeClass = 'badge-' . $meeting->status->color();
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $meeting->status->label() }}</span>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Your Status') }}</div>
                                <div class="mt-1">
                                    @php
                                        $investorStatus = $meeting->investors->where('id', auth()->id())->first()->pivot->status ?? null;
                                        $badgeClass = match ($investorStatus) {
                                            \App\Enums\InvestorStatus::CONFIRMED->value => 'badge-success',
                                            \App\Enums\InvestorStatus::REFUSED->value => 'badge-error',
                                            \App\Enums\InvestorStatus::PENDING->value => 'badge-warning',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ __('You:') }} {{ $investorStatus ?? __('Unknown') }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Date & Time') }}</div>
                                <div class="mt-1 text-sm">
                                    {{ $meeting->timeSlot->start_time->format('d/m/Y H:i') }} -
                                    {{ $meeting->timeSlot->end_time->format('H:i') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Room') }}</div>
                                <div class="mt-1 text-sm">
                                    @if ($meeting->room)
                                        {{ $meeting->room->name }}<br>
                                        <span class="text-base-content/50">{{ $meeting->room->location }}</span>
                                    @else
                                        <span class="text-base-content/50">{{ __('Virtual Meeting') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Meeting Type') }}</div>
                                <div class="mt-1 text-sm">
                                    @if ($meeting->is_one_on_one)
                                        <span class="badge badge-primary">{{ __('1-on-1') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Group') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Issuer Information -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Issuer Information') }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Name') }}</div>
                                <div class="mt-1 text-sm">
                                    {{ $meeting->issuer->first_name }} {{ $meeting->issuer->name }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Organization') }}</div>
                                <div class="mt-1 text-sm">
                                    {{ $meeting->issuer->organization->name ?? __('No Organization') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Position') }}</div>
                                <div class="mt-1 text-sm">
                                    {{ $meeting->issuer->position ?? __('Not specified') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Email') }}</div>
                                <div class="mt-1 text-sm">
                                    {{ $meeting->issuer->email }}
                                </div>
                            </div>

                            @if ($meeting->issuer->phone)
                                <div>
                                    <div class="text-sm font-medium text-base-content/70">{{ __('Phone') }}</div>
                                    <div class="mt-1 text-sm">
                                        {{ $meeting->issuer->phone }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions Panel -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Actions') }}</h2>
                        <div class="space-y-3 mt-4">
                            @if ($meeting->status->value == 'scheduled')
                                <button type="button" class="btn btn-primary w-full" x-data="" x-on:click="$dispatch('open-modal', 'confirm-attendance')">
                                    {{ __('Confirm Attendance') }}
                                </button>
                                <button type="button" class="btn btn-error w-full" x-data="" x-on:click="$dispatch('open-modal', 'cancel-attendance')">
                                    {{ __('Cancel Attendance') }}
                                </button>
                            @endif

                            <a href="{{ route('investor.issuers.show', $meeting->issuer->id) }}" class="btn btn-outline btn-primary w-full">
                                {{ __('View Issuer Profile') }}
                            </a>

                            <a href="{{ route('investor.meetings.index') }}" class="btn btn-outline w-full">
                                {{ __('Back to Meetings') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Questions Panel -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title text-primary">{{ __('Questions to Issuer') }}</h2>
                            @if ($questions && $questions->count() > 0)
                                <span class="badge badge-primary">{{ $questions->count() }}</span>
                            @endif
                        </div>

                        @if ($questions && $questions->count() > 0)
                            <div class="mb-4 mt-4">
                                <h3 class="text-sm font-medium text-base-content mb-2">{{ __('Your previous questions') }}</h3>
                                <div class="space-y-4">
                                    @foreach ($questions as $question)
                                        <div class="bg-base-200 p-3 rounded-md">
                                            <p class="text-sm">{{ $question->question }}</p>
                                            <p class="text-xs text-base-content/60 mt-1">
                                                {{ $question->created_at->format('d/m/Y H:i') }}</p>
                                            @if ($question->response)
                                                <div class="mt-2 pt-2 border-t border-base-300">
                                                    <p class="text-xs font-medium text-base-content/70">{{ __('Response:') }}</p>
                                                    <p class="text-sm mt-1">{{ $question->response }}</p>
                                                </div>
                                            @else
                                                <p class="text-xs text-warning mt-1">{{ __('Awaiting response') }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-base-content/70 italic mt-2">{{ __('No questions have been asked yet.') }}</p>
                        @endif

                        <form method="POST" action="{{ route('investor.questions.store') }}" class="mt-3">
                            @csrf
                            <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
                            <div class="mb-3">
                                <label class="label">
                                    <span class="label-text">{{ __('Ask a new question') }}</span>
                                </label>
                                <textarea class="textarea textarea-bordered w-full" id="question" name="question" rows="3"
                                    placeholder="{{ __('Type your question for the issuer here...') }}"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-full">
                                {{ __('Submit Question') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Confirm Attendance Modal -->
    <x-modal name="confirm-attendance" :show="$errors->isNotEmpty()" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-primary">
                {{ __('Confirm Attendance') }}
            </h2>

            <p class="mt-3 text-sm text-base-content/70">
                {{ __('Are you sure you want to confirm your attendance to this meeting?') }}
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>

                {{-- <form action="{{ route('investor.meetings.update-status', $meeting) }}" method="POST"> --}}
                <form action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="{{ \App\Enums\InvestorStatus::CONFIRMED->value }}">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Confirm Attendance') }}
                    </button>
                </form>
            </div>
        </div>
    </x-modal>

    <!-- Cancel Attendance Modal -->
    <x-modal name="cancel-attendance" :show="$errors->isNotEmpty()" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-error">
                {{ __('Cancel Attendance') }}
            </h2>

            <p class="mt-3 text-sm text-base-content/70">
                {{ __('Are you sure you want to cancel your attendance to this meeting?') }}
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>

                {{-- <form action="{{ route('investor.meetings.update-status', $meeting) }}" method="POST"> --}}
                <form action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="{{ \App\Enums\InvestorStatus::REFUSED->value }}">
                    <button type="submit" class="btn btn-error">
                        {{ __('Confirm Cancellation') }}
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
</x-app-layout>
