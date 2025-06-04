@props(['meetings', 'headers', 'empty' => 'No meetings found'])

<div class="card bg-base-100 shadow-md overflow-x-auto">
    <table class="table">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <x-table.sortable-header label="{{ $header['content'] }}" field="{{ $header['sortField'] ?? '' }}"
                        sortable="{{ $header['sortable'] ?? false }}"
                        align="{{ str_replace('text-', '', $header['align']) }}" />
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($meetings as $meeting)
                <tr class="meeting-row">
                    <td class="font-medium">
                        <span class="badge badge-neutral">#{{ $meeting->id }}</span>
                    </td>
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
                            <span class="text-base-content/50">{{ __('No Room') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', $meeting->issuer) }}"
                            class="text-primary font-medium link">
                            {{ $meeting->issuer->first_name }} {{ $meeting->issuer->name }}
                        </a>
                        <div class="mt-1 text-base-content/70">
                            {{ $meeting->issuer->organization->name ?? 'No Organization' }}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-primary rounded-full">{{ $meeting->investors_count }}</span>
                    </td>
                    <td class="text-center">
                        @if ($meeting->is_one_on_one)
                            <span class="badge badge-primary">{{ __('1-on-1') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ __('Group') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span
                            class="badge badge-{{ $meeting->status->color() }}">{{ $meeting->status->label() }}</span>
                    </td>
                    <td class="text-center">
                        <div class="badge badge-neutral">{{ $meeting->questions_count }}</div>
                    </td>
                    <td class="text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-sm font-medium text-base-content">
                                {{ $meeting->created_at->diffForHumans() }}
                            </span>
                            <span class="text-xs text-base-content/70 mt-1">
                                {{ $meeting->created_at->format('d-m-Y H:i') }}
                            </span>
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="flex gap-2 items-center justify-end">
                            <a href="{{ route('admin.attendance.scanner', $meeting) }}" class="btn btn-sm btn-warning"
                                title="{{ __('Scan QR Code') }}">
                                <x-heroicon-s-qr-code class="size-4" />
                            </a>
                            <a href="{{ route('admin.meetings.show', $meeting) }}"
                                class="btn btn-sm btn-primary">{{ __('View') }}</a>
                            <a href="{{ route('admin.meetings.edit', $meeting) }}"
                                class="btn btn-sm btn-success">{{ __('Edit') }}</a>
                            <button type="button" class="btn btn-sm btn-square btn-error"
                                @click="$dispatch('open-modal', 'delete-meeting-{{ $meeting->id }}')"><x-heroicon-s-trash
                                    class="size-4" /></button>
                        </div>
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
