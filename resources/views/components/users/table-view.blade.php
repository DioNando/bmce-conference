@props(['users', 'headers', 'empty' => 'No users found'])

<div class="card bg-base-100 shadow-md overflow-x-auto">
    <table class="table">
        <thead>
            <tr>
                <th class="w-10 pl-6 pt-2">
                    {{-- ! <label class="cursor-pointer">
                        <input type="checkbox" id="select-all-users"
                            class="checkbox checkbox-sm checkbox-primary"
                            x-on:change="toggleAll($event)">
                    </label> --}}
                </th>
                @foreach ($headers as $header)
                    <x-table.sortable-header label="{{ $header['content'] }}"
                        field="{{ $header['sortField'] ?? '' }}"
                        sortable="{{ $header['sortable'] ?? false }}"
                        align="{{ str_replace('text-', '', $header['align']) }}" />
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr class="user-row"
                    :class="{ 'bg-primary-content': selectedUsers.includes({{ $user->id }}) }">
                    <td class="w-10 pl-6 pt-2">
                        <label class="cursor-pointer">
                            <input type="checkbox" value="{{ $user->id }}"
                                class="user-checkbox checkbox checkbox-sm checkbox-primary"
                                x-on:change="toggleUser({{ $user->id }})"
                                :checked="selectedUsers.includes({{ $user->id }})">
                        </label>
                    </td>
                    <td class="font-medium">
                        <a href="{{ route('admin.users.show', $user) }}" class="link link-primary">
                            {{ $user->name }}
                        </a>
                    </td>
                    <td class="font-medium">
                        <a href="{{ route('admin.users.show', $user) }}" class="link link-primary">
                            {{ $user->first_name }}
                        </a>
                    </td>
                    <td>
                        <div class="text-base-content">
                            {{ $user->email }}
                        </div>
                        <div class="mt-1 text-base-content/70">
                            {{ $user->phone ?? '-' }}
                        </div>
                    </td>
                    <td class="text-center">
                        @if ($user->hasRole(App\Enums\UserRole::ISSUER->value))
                            <span class="badge badge-warning">
                                {{ App\Enums\UserRole::ISSUER->label() }}
                            </span>
                        @elseif($user->hasRole(App\Enums\UserRole::INVESTOR->value))
                            <span class="badge badge-primary">
                                {{ App\Enums\UserRole::INVESTOR->label() }}
                            </span>
                        @else
                            <span class="badge">Unknown</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $user->organization?->origin?->label() ?? '-' }}
                    </td>
                    <td class="text-center">
                        @if ($user->status)
                            <div class="badge badge-success">
                                {{ __('Active') }}
                            </div>
                        @else
                            <div class="badge badge-neutral">
                                {{ __('Inactive') }}
                            </div>
                        @endif
                    </td>
                    <td>
                        {{ $user->organization->name ?? '-' }}
                    </td>
                    <td class="text-right">
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button"
                                class="btn btn-ghost btn-xs btn-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z">
                                    </path>
                                </svg>
                            </div>
                            <ul tabindex="0"
                                class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-64">
                                <li>
                                    <a href="{{ route('admin.users.show', $user) }}">
                                        <x-heroicon-s-eye class="size-4" />
                                        {{ __('View') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="flex items-center">
                                        <x-heroicon-s-pencil class="size-4" />
                                        <span>{{ __('Edit') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <form
                                        action="{{ route('admin.users.send-activation-email', $user) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full gap-2">
                                            <x-heroicon-s-envelope class="size-4" />
                                            <span>
                                                {{ $user->status ? __('Resend activation email') : __('Send activation email') }}</span>
                                        </button>
                                    </form>
                                </li>

                                @if ($user->hasRole(App\Enums\UserRole::ISSUER->value))
                                    <li>
                                        <a href="{{ route('admin.users.schedule', $user) }}"
                                            class="flex items-center">
                                            <x-heroicon-s-calendar class="size-4" />
                                            <span>{{ __('Schedule') }}</span>
                                        </a>
                                    </li>

                                    <li>
                                        <form
                                            action="{{ route('admin.users.generate-time-slots', $user) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center w-full gap-2">
                                                <x-heroicon-s-clock class="size-4" />
                                                <span>{{ __('Generate TimeSlots') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif

                                <li>
                                    <button type="button"
                                        class="flex items-center w-full gap-2 text-error"
                                        @click="$dispatch('open-modal', 'delete-user-{{ $user->id }}')">
                                        <x-heroicon-s-trash class="size-4" />
                                        <span>{{ __('Delete') }}</span>
                                        <span class="sr-only">
                                            {{ __('Delete') }} {{ $user->name }}
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + 1 }}"
                        class="text-center py-5 text-base-content/70">
                        {{ $empty }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
