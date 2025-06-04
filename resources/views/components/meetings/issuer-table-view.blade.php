@props(['meetings', 'headers', 'empty' => 'No meetings found'])

<div class="card bg-base-100 shadow-md overflow-x-auto">
    <table class="table">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th class="{{ $header['align'] }}">{{ __($header['content']) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($meetings as $meeting)
                <tr>
                    <td class="font-medium">
                        <div class="text-base-content">
                            {{ $meeting->timeSlot->date->format('l, F j, Y') }}
                        </div>
                        <div class="mt-1 text-base-content/70">
                            {{ $meeting->timeSlot->start_time->format('H:i') }} -
                            {{ $meeting->timeSlot->end_time->format('H:i') }}
                        </div>
                    </td>
                    <td>
                        @if ($meeting->room)
                            {{ $meeting->room->name }}
                        @else
                            <span class="text-base-content/50">{{ __('Virtual Meeting') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-warning rounded-full">
                            {{ $meeting->investors_count }}</span>
                    </td>
                    <td class="text-center">
                        @if ($meeting->is_one_on_one)
                            <span class="badge badge-primary">{{ __('One-on-One') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ __('Group') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $meeting->status->color() }}">{{ $meeting->status->label() }}</span>
                    </td>
                    <td class="text-center">
                        <div class="badge badge-neutral">{{ $meeting->questions_count }}</div>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('issuer.meetings.show', $meeting) }}" class="btn btn-sm btn-primary">
                            <x-heroicon-s-eye class="size-4" />
                            {{ __('View Details') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center py-5 text-base-content/70">
                        {{ $empty }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
