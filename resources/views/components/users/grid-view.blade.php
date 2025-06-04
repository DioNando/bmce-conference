@props(['users', 'empty' => 'No users found'])

{{-- ! <div class="flex justify-between items-center mb-5">
    <div class="form-control">
        <label class="cursor-pointer flex items-center gap-2">
            <input type="checkbox" id="select-all-users" class="checkbox checkbox-sm checkbox-primary"
                x-on:change="toggleAll($event)">
            <span class="text-sm font-medium">{{ __('Select All') }}</span>
        </label>
    </div>
</div> --}}

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($users as $user)
        <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden"
            :class="{ 'border-primary': selectedUsers.includes({{ $user->id }}) }">
            <div class="bg-primary/10 text-primary px-5 py-3 text-sm flex justify-between items-center">
                @php
                    $roleBadge = '';
                    $roleText = 'Unknown';

                    if ($user->hasRole(App\Enums\UserRole::ISSUER->value)) {
                        $roleBadge = 'badge-warning';
                        $roleText = App\Enums\UserRole::ISSUER->label();
                    } elseif ($user->hasRole(App\Enums\UserRole::INVESTOR->value)) {
                        $roleBadge = 'badge-primary';
                        $roleText = App\Enums\UserRole::INVESTOR->label();
                    }
                @endphp

                <div class="flex items-center gap-2">
                    <div class="form-control">
                        <label class="cursor-pointer">
                            <input type="checkbox" value="{{ $user->id }}"
                                class="user-checkbox checkbox checkbox-sm checkbox-primary"
                                x-on:change="toggleUser({{ $user->id }})"
                                :checked="selectedUsers.includes({{ $user->id }})">
                        </label>
                    </div>
                    <span class="badge {{ $roleBadge }} badge-sm">{{ $roleText }}</span>
                </div>

                <div class="flex-shrink-0">
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-xs btn-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                class="inline-block w-4 h-4 stroke-current">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z">
                                </path>
                            </svg>
                        </div>
                        <ul tabindex="0"
                            class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-64 text-gray-900">
                            <li>
                                <a href="{{ route('admin.users.show', $user) }}">
                                    <x-heroicon-s-eye class="size-4" />
                                    {{ __('View') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.users.edit', $user) }}" class="flex items-center">
                                    <x-heroicon-s-pencil class="size-4" />
                                    <span>{{ __('Edit') }}</span>
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('admin.users.send-activation-email', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full gap-2">
                                        <x-heroicon-s-envelope class="size-4" />
                                        <span>{{ $user->status ? __('Resend activation email') : __('Send activation email') }}</span>
                                    </button>
                                </form>
                            </li>

                            @if ($user->hasRole(App\Enums\UserRole::ISSUER->value))
                                <li>
                                    <a href="{{ route('admin.users.schedule', $user) }}" class="flex items-center">
                                        <x-heroicon-s-calendar class="size-4" />
                                        <span>{{ __('Schedule') }}</span>
                                    </a>
                                </li>

                                <li>
                                    <form action="{{ route('admin.users.generate-time-slots', $user) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full gap-2">
                                            <x-heroicon-s-clock class="size-4" />
                                            <span>{{ __('Generate TimeSlots') }}</span>
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li>
                                <button type="button" class="flex items-center w-full gap-2 text-error"
                                    @click="$dispatch('open-modal', 'delete-user-{{ $user->id }}')">
                                    <x-heroicon-s-trash class="size-4" />
                                    <span>{{ __('Delete') }}</span>
                                    <span class="sr-only">{{ __('Delete') }} {{ $user->name }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body p-5">
                <div class="flex flex-row items-start mb-3">
                    <div class="flex-shrink-0 mr-3">
                        <div class="bg-primary/10 text-primary rounded-full w-14 h-14 flex items-center justify-center">
                            <span
                                class="text-lg font-bold font-mono">{{ substr($user->first_name, 0, 1) }}{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1 text-base-content">
                            <a href="{{ route('admin.users.show', $user) }}"
                                class="hover:text-primary transition-colors">
                                {{ $user->first_name }} {{ $user->name }}
                            </a>
                        </h3>
                        <p class="text-sm text-primary font-medium">
                            {{ $user->organization->name ?? __('No Organization') }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-xs text-base-content/80">
                        <x-heroicon-s-envelope class="size-4 text-primary/70" />
                        <span>{{ $user->email }}</span>
                    </div>

                    @if ($user->phone)
                        <div class="flex items-center gap-2 text-xs text-base-content/80">
                            <x-heroicon-s-phone class="size-4 text-primary/70" />
                            <span>{{ $user->phone }}</span>
                        </div>
                    @endif

                    @if ($user->organization && $user->organization->origin)
                        <div class="flex items-center gap-2 text-xs text-base-content/80">
                            <x-heroicon-s-globe-europe-africa class="size-4 text-primary/70" />
                            <span>{{ $user->organization->country->name_en ?? __('Not specified') }}</span>
                            <span class="badge badge-xs">
                                {{ $user->organization->origin->label() ?? '-' }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="card-actions justify-between items-center mt-auto">
                    <a href="{{ route('admin.users.show', $user) }}"
                        class="text-primary hover:text-primary/70 text-sm font-medium flex items-center gap-1">
                        <x-heroicon-s-user class="size-4" />
                        {{ __('View Profile') }}
                    </a>

                    @if ($user->status)
                        <div class="badge badge-success">
                            {{ __('Active') }}
                        </div>
                    @else
                        <div class="badge badge-neutral">
                            {{ __('Inactive') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-8">
            <x-heroicon-s-exclamation-triangle class="size-16 mx-auto text-base-content/30" />
            <h3 class="mt-2 text-sm font-medium text-base-content">{{ $empty }}</h3>
            <p class="mt-1 text-sm text-base-content/70">
                {{ __('No users are currently available in the system.') }}
            </p>
        </div>
    @endforelse
</div>
