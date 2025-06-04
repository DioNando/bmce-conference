<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('issuer.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('issuer.meetings.index') }}" class="text-primary">{{ __('Meetings') }}</a></li>
            <li>{{ __('Meeting Details') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-primary">
                {{ __('Meeting on :date', ['date' => $meeting->timeSlot->date->format('F j, Y')]) }}
            </h1>
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
                                        $badgeClass = match($meeting->status->value) {
                                            \App\Enums\MeetingStatus::SCHEDULED->value => 'badge-success',
                                            \App\Enums\MeetingStatus::COMPLETED->value => 'badge-success',
                                            \App\Enums\MeetingStatus::CANCELLED->value => 'badge-error',
                                            \App\Enums\MeetingStatus::PENDING->value => 'badge-warning',
                                            \App\Enums\MeetingStatus::CONFIRMED->value => 'badge-info',
                                            \App\Enums\MeetingStatus::DECLINED->value => 'badge-error',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ __($meeting->status->label()) }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Meeting Type') }}</div>
                                <div class="mt-1 text-sm">
                                    @if ($meeting->is_one_on_one)
                                        <span class="badge badge-info">{{ __('One-on-One (individual)') }}</span>
                                    @else
                                        <span class="badge badge-success">{{ __('Group') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Date & Time') }}</div>
                                <div class="mt-1 text-sm">
                                    {{ $meeting->timeSlot->date->format('l, F j, Y') }}<br>
                                    {{ $meeting->timeSlot->start_time->format('H:i') }} -
                                    {{ $meeting->timeSlot->end_time->format('H:i') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Room') }}</div>
                                <div class="mt-1 text-sm">
                                    @if($meeting->room)
                                        {{ $meeting->room->name }}<br>
                                        <span class="text-base-content/70">{{ $meeting->room->location }}</span>
                                    @else
                                        <span class="text-base-content/50">{{ __('Virtual Meeting') }}</span>
                                    @endif
                                </div>
                            </div>

                            @if ($meeting->notes)
                                <div class="col-span-2">
                                    <div class="text-sm font-medium text-base-content/70">{{ __('Notes') }}</div>
                                    <div class="mt-1 text-sm whitespace-pre-line">
                                        {{ $meeting->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Investors List -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title text-primary">{{ __('Investors') }}</h2>
                            <span class="badge badge-warning">
                                {{ $meeting->investors->count() }}
                                {{ Str::plural('investor', $meeting->investors->count()) }}
                            </span>
                        </div>

                        @if ($meeting->investors->isEmpty())
                            <p class="text-base-content/70 italic">{{ __('No investors are attending this meeting.') }}</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                @foreach ($meeting->investors as $investor)
                                    <div class="card card-bordered card-compact shadow-md">
                                        <div class="card-body">
                                            <h3 class="card-title text-base">{{ $investor->first_name }}
                                                {{ $investor->name }}</h3>
                                            <div class="text-base-content">
                                                {{ $investor->organization->name ?? 'No Organization' }}
                                            </div>
                                            <div class="text-sm text-base-content/70 mt-1">
                                                {{ $investor->email }}
                                            </div>
                                            @php
                                                $meetingInvestor = $investor->pivot;
                                                $status = $meetingInvestor->status;
                                                $badgeClass = match($status) {
                                                    \App\Enums\InvestorStatus::PENDING->value => 'badge-warning',
                                                    \App\Enums\InvestorStatus::CONFIRMED->value => 'badge-success',
                                                    \App\Enums\InvestorStatus::REFUSED->value => 'badge-error',
                                                    default => 'badge-neutral',
                                                };
                                            @endphp

                                            <div class="mt-1 flex items-center">
                                                <span class="badge {{ $badgeClass }}">
                                                    @switch($status)
                                                        @case(\App\Enums\InvestorStatus::PENDING->value)
                                                            {{ __('Pending') }}
                                                        @break

                                                        @case(\App\Enums\InvestorStatus::CONFIRMED->value)
                                                            {{ __('Confirmed') }}
                                                        @break

                                                        @case(\App\Enums\InvestorStatus::REFUSED->value)
                                                            {{ __('Refused') }}
                                                        @break

                                                        @default
                                                            {{ __('Unknown') }}
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div class="card-actions justify-end mt-2">


                                                <!-- Change status buttons -->
                                                <form
                                                    action="{{ route('issuer.meetings.update-investor-status', ['meeting' => $meeting->id, 'investor' => $investor->id]) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="flex gap-2">
                                                        @if ($status != \App\Enums\InvestorStatus::CONFIRMED->value)                                                                <button type="submit" name="status"
                                                                value="{{ \App\Enums\InvestorStatus::CONFIRMED->value }}"
                                                                class="btn btn-xs btn-success">
                                                                <x-heroicon-s-check class="size-4 mr-1.5" /> {{ __('Confirm') }}
                                                            </button>
                                                        @endif

                                                        @if ($status != \App\Enums\InvestorStatus::REFUSED->value)
                                                            <button type="submit" name="status"
                                                                value="{{ \App\Enums\InvestorStatus::REFUSED->value }}"
                                                                class="btn btn-xs btn-error">
                                                                <x-heroicon-s-x-mark class="size-4 mr-1.5" /> {{ __('Refuse') }}
                                                            </button>
                                                        @endif

                                                        @if ($status != \App\Enums\InvestorStatus::CONFIRMED->value && $meeting->timeSlot->date <= now())
                                                            <button type="submit" name="status"
                                                                value="{{ \App\Enums\InvestorStatus::CONFIRMED->value }}"
                                                                class="btn btn-xs btn-info">
                                                                <x-heroicon-s-user class="size-4 mr-1.5" /> {{ __('Mark Attended') }}
                                                            </button>
                                                        @endif
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Questions Panel -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h2 class="card-title text-primary">{{ __('Questions') }}</h2>
                            <span class="badge badge-warning">
                                {{ $meeting->questions->count() }}
                            </span>
                        </div>
                        @if ($meeting->questions->isEmpty())
                            <p class="text-base-content/70 italic">{{ __('No questions have been asked yet.') }}</p>
                        @else
                            <div class="space-y-4 mt-4">
                                @foreach ($meeting->questions as $question)
                                    <div class="border border-base-300 rounded-lg p-3">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center">
                                                <span class="font-medium">
                                                    {{ $question->investor->first_name }} {{ $question->investor->name }}
                                                </span>
                                                <span class="mx-2 text-base-content/70">&#8226;</span>
                                                <span class="text-xs text-base-content/70">
                                                    {{ $question->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            @if ($question->response)
                                                <span class="badge badge-success">
                                                    {{ __('Answered') }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    {{ __('Pending') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="whitespace-pre-line">{{ $question->question }}</p>

                                        @if ($question->response)
                                            <div class="mt-3 pt-3 border-t border-base-200">
                                                <div class="flex items-center mb-1">
                                                    <span class="text-sm font-medium text-base-content/70">{{ __('Your response:') }}</span>
                                                    <span class="mx-2 text-base-content/70">&#8226;</span>
                                                    <span class="text-xs text-base-content/70">
                                                        {{ $question->updated_at->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>
                                                <p class="bg-base-200 p-3 rounded-md">
                                                    {{ $question->response }}
                                                </p>
                                            </div>
                                        @else
                                            <div class="mt-3 border-t border-base-200 pt-3">
                                                <form action="{{ route('issuer.questions.answer', $question) }}"
                                                    method="POST">
                                                    @csrf
                                                    <textarea name="response" rows="2"
                                                        placeholder="{{ __('Type your response...') }}"
                                                        class="textarea textarea-bordered w-full text-sm" required></textarea>
                                                    <div class="mt-2 text-right">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            {{ __('Reply') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions Panel -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Actions') }}</h2>
                        <div class="space-y-3 mt-4">
                            @if ($meeting->status->value != \App\Enums\MeetingStatus::CANCELLED->value)
                                <button type="button" class="btn btn-error w-full" x-data="" x-on:click="$dispatch('open-modal', 'cancel-meeting')">
                                    {{ __('Cancel Meeting') }}
                                </button>
                            @endif

                            @if ($meeting->status->value == \App\Enums\MeetingStatus::CANCELLED->value)
                                <form action="{{ route('issuer.meetings.update-status', $meeting) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status"
                                        value="{{ \App\Enums\MeetingStatus::SCHEDULED->value }}">
                                    <button type="submit" class="btn btn-warning w-full">
                                        {{ __('Reactivate Meeting') }}
                                    </button>
                                </form>
                            @endif

                            @if ($meeting->timeSlot->date <= now() && $meeting->status->value != \App\Enums\MeetingStatus::COMPLETED->value)
                                <form action="{{ route('issuer.meetings.update-status', $meeting) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status"
                                        value="{{ \App\Enums\MeetingStatus::COMPLETED->value }}">
                                    <button type="submit" class="btn btn-success w-full">
                                        {{ __('Mark as Completed') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cancel Meeting Modal -->
    <x-modal name="cancel-meeting" :show="$errors->isNotEmpty()" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-error">
                {{ __('Cancel Meeting') }}
            </h2>

            <p class="mt-3 text-sm text-base-content/70">
                {{ __('Are you sure you want to cancel this meeting? This action cannot be undone and all investors will be notified.') }}
            </p>

            @if ($meeting->investors->isNotEmpty())
                <div class="alert alert-warning mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>{{ __('This meeting has :count confirmed investors.', ['count' => $meeting->investors->where('pivot.status', \App\Enums\InvestorStatus::CONFIRMED->value)->count()]) }}</span>
                </div>
            @endif

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>

                <form action="{{ route('issuer.meetings.update-status', $meeting) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="{{ \App\Enums\MeetingStatus::CANCELLED->value }}">
                    <button type="submit" class="btn btn-error">
                        {{ __('Confirm Cancellation') }}
                    </button>
                </form>
            </div>
        </div>
    </x-modal>
</x-app-layout>
