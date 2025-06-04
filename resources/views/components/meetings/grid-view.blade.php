@props(['meetings', 'empty' => 'No meetings found'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($meetings as $meeting)
        <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300">
            <!-- Card Header with ID, Status and Actions -->
            <div class="card-body p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-2">
                        <div class="badge badge-neutral text-xs font-mono">#{{ $meeting->id }}</div>
                        <div class="badge badge-{{ $meeting->status->color() }}">{{ $meeting->status->label() }}</div>
                    </div>
                    <div class="flex items-center gap-2">

                        <!-- Actions Dropdown -->
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-xs btn-circle">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </div>
                            <ul tabindex="0"
                                class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-64">
                                <li>
                                    <a href="{{ route('admin.meetings.show', $meeting) }}">
                                        <x-heroicon-s-eye class="size-4" />
                                        {{ __('View') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.meetings.edit', $meeting) }}">
                                        <x-heroicon-s-pencil class="size-4" />
                                        {{ __('Edit') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.meetings.investors', $meeting) }}">
                                        <x-heroicon-s-user-group class="size-4" />
                                        {{ __('Investors') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.attendance.scanner', ['meeting' => $meeting->id]) }}">
                                        <x-heroicon-s-qr-code class="size-4" />
                                        {{ __('Scan QR Code') }}
                                    </a>
                                </li>
                                <li>
                                    <button type="button" class="flex items-center w-full gap-2 text-error"
                                        @click="$dispatch('open-modal', 'delete-meeting-{{ $meeting->id }}')">
                                        <x-heroicon-s-trash class="size-4" />
                                        <span>{{ __('Delete') }}</span>
                                        <span class="sr-only">{{ __('Delete') }} {{ $meeting->title }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Meeting Icon and Title -->
                <div class="flex items-start gap-4 mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary/10 text-primary rounded-lg w-12 h-12 flex items-center justify-center">
                            <x-heroicon-s-calendar class="size-6" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-lg mb-1 text-base-content truncate">
                            <a href="{{ route('admin.meetings.show', $meeting) }}"
                                class="hover:text-primary transition-colors">
                                {{ $meeting->title }}
                            </a>
                        </h3>
                        <p class="text-lg font-semibold text-base-content/70 truncate">
                            {{ $meeting->issuer->name . ' ' . $meeting->issuer->first_name ?? __('No Issuer') }}
                        </p>
                    </div>
                </div>

                <!-- Meeting Details -->
                <div class="space-y-3 mb-4">
                    <div class="flex items-center gap-3 text-sm text-base-content/80">
                        <x-heroicon-s-calendar-days class="size-4 text-primary/70 flex-shrink-0" />
                        <span>{{ $meeting->timeSlot->date->format('M d, Y') }}</span>
                    </div>

                    <div class="flex items-center gap-3 text-sm text-base-content/80">
                        <x-heroicon-s-clock class="size-4 text-primary/70 flex-shrink-0" />
                        <span>{{ $meeting->timeSlot->start_time->format('H:i') }} -
                            {{ $meeting->timeSlot->end_time->format('H:i') }}</span>
                    </div>

                    @if ($meeting->room)
                        <div class="flex items-center gap-3 text-sm text-base-content/80">
                            <x-heroicon-s-map-pin class="size-4 text-primary/70 flex-shrink-0" />
                            <span class="truncate">{{ $meeting->room->name }}</span>
                        </div>
                    @endif

                    <div class="flex items-center gap-3 text-sm text-base-content/80">
                        <x-heroicon-s-user-group class="size-4 text-primary/70 flex-shrink-0" />
                        <span>{{ $meeting->investors_count }} {{ __('Investors') }}</span>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="flex justify-between items-center pt-4 border-t border-base-300">
                    <a href="{{ route('admin.meetings.show', $meeting) }}" class="btn btn-primary btn-sm rounded-full">
                        <x-heroicon-s-eye class="size-4" />
                        {{ __('View Details') }}
                    </a>

                    <div class="text-xs text-base-content/60">
                        {{ $meeting->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <div class="mx-auto w-24 h-24 bg-base-200 rounded-full flex items-center justify-center mb-4">
                <x-heroicon-s-calendar class="size-12 text-base-content/30" />
            </div>
            <h3 class="text-lg font-medium text-base-content mb-2">{{ $empty }}</h3>
            <p class="text-sm text-base-content/70 max-w-md mx-auto">
                {{ __('No meetings are currently available in the system. Create your first meeting to get started.') }}
            </p>
        </div>
    @endforelse
</div>
