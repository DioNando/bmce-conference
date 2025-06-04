<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="text-primary">{{ __('Accounts') }}</a></li>
            <li>{{ $user->name }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl text-primary">
                {{ __('Account Details') }}
                @if ($user->status)
                    <span class="badge badge-success">{{ __('Active') }}</span>
                @else
                    <span class="badge badge-neutral">{{ __('Inactive') }}</span>
                @endif
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Details -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Personal Information') }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('First Name') }}</div>
                                <div class="mt-1 text-base">{{ $user->first_name }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Last Name') }}</div>
                                <div class="mt-1 text-base">{{ $user->name }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Email') }}</div>
                                <div class="mt-1 text-base">
                                    <a href="mailto:{{ $user->email }}" class="link link-primary">
                                        {{ $user->email }}
                                    </a>
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Phone') }}</div>
                                <div class="mt-1 text-base">
                                    @if ($user->phone)
                                        <a href="tel:{{ $user->phone }}"
                                            class="link link-primary">{{ $user->phone }}</a>
                                    @else
                                        <span class="text-base-content/50">-</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Position') }}</div>
                                <div class="mt-1 text-base">{{ $user->position ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Role') }}</div>
                                <div class="mt-1">
                                    @if ($user->hasRole(App\Enums\UserRole::ISSUER->value))
                                        <span
                                            class="badge badge-warning">{{ App\Enums\UserRole::ISSUER->label() }}</span>
                                    @elseif($user->hasRole(App\Enums\UserRole::INVESTOR->value))
                                        <span
                                            class="badge badge-primary">{{ App\Enums\UserRole::INVESTOR->label() }}</span>
                                    @else
                                        <span class="badge badge-neutral">{{ __('Unknown') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organization Information -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Organization Information') }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="flex-shrink-0">
                                @if ($user->organization?->logo_path)
                                    <img class="h-16 w-16 rounded-full object-cover"
                                        src="{{ $user->organization->logo_url }}"
                                        alt="{{ $user->organization?->name }}">
                                @else
                                    <div
                                        class="h-16 w-16 rounded-full {{ $user->hasRole(App\Enums\UserRole::ISSUER->value) ? 'bg-warning' : ($user->hasRole(App\Enums\UserRole::INVESTOR->value) ? 'bg-primary' : 'bg-success') }} flex items-center justify-center text-white text-xl font-semibold">
                                        {{ substr($user->organization?->name ?? '-', 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Organization Name') }}
                                </div>
                                <div class="mt-1 text-base">{{ $user->organization?->name ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Origin') }}</div>
                                <div class="mt-1 text-base">{{ $user->organization?->origin?->label() ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Organization Type') }}
                                </div>
                                <div class="mt-1 text-base">
                                    {{ $user->organization?->organization_type?->englishLabel() ?? '-' }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Country') }}</div>
                                <div class="mt-1 text-base">{{ $user->organization?->country?->name_en ?? '-' }}</div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- User Meetings -->
                <div class="mt-8">
                    <div class="card card-bordered bg-base-100 shadow-lg">
                        <div class="card-body">
                            <h2 class="card-title text-primary">{{ __('Meetings') }}</h2>

                            @if ($user->hasRole(App\Enums\UserRole::ISSUER->value))
                                <!-- Issuer Meetings -->
                                @if ($user->issuerMeetings->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Time') }}</th>
                                                    <th>{{ __('Room') }}</th>
                                                    <th>{{ __('Investors') }}</th>
                                                    <th class="text-center">{{ __('Status') }}</th>
                                                    <th class="text-end">{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user->issuerMeetings as $meeting)
                                                    <tr>
                                                        <td>{{ $meeting->timeSlot->date->format('d/m/Y') }}</td>
                                                        <td>{{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                            {{ $meeting->timeSlot->end_time->format('H:i') }}</td>
                                                        <td>{{ $meeting->room->name ?? '-' }}</td>
                                                        <td>
                                                            <div class="flex -space-x-1">
                                                                @foreach ($meeting->investors->take(3) as $investor)
                                                                    <div
                                                                        class="size-6 rounded-full ring-2 ring-white bg-primary flex items-center justify-center text-white text-xs font-semibold">
                                                                        {{ substr($investor->name, 0, 1) }}
                                                                    </div>
                                                                @endforeach
                                                                @if ($meeting->investors->count() > 3)
                                                                    <div
                                                                        class="size-6 rounded-full ring-2 ring-white bg-primary flex items-center justify-center text-white text-xs font-semibold">
                                                                        +{{ $meeting->investors->count() - 3 }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-{{ $meeting->status->color() }}">
                                                                {{ $meeting->status->label() }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="{{ route('admin.meetings.show', $meeting) }}"
                                                                class="btn btn-sm btn-primary">
                                                                {{ __('View') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        {{ __('No meetings found for this issuer.') }}
                                    </div>
                                @endif
                            @elseif($user->hasRole(App\Enums\UserRole::INVESTOR->value))
                                <!-- Investor Meetings -->
                                @if ($user->investorMeetings->count() > 0)
                                    <div class="card overflow-x-auto">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Time') }}</th>
                                                    <th>{{ __('Issuer') }}</th>
                                                    <th>{{ __('Room') }}</th>
                                                    <th class="text-center">{{ __('Status') }}</th>
                                                    <th class="text-end">{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user->investorMeetings as $meeting)
                                                    <tr>
                                                        <td>{{ $meeting->timeSlot->date->format('d/m/Y') }}</td>
                                                        <td>{{ $meeting->timeSlot->start_time->format('H:i') }} -
                                                            {{ $meeting->timeSlot->end_time->format('H:i') }}</td>
                                                        <td>
                                                            <div class="flex items-center gap-2">
                                                                <div
                                                                    class="size-6 rounded-full ring-2 ring-white bg-warning flex items-center justify-center text-white text-xs font-semibold">
                                                                    {{ substr($meeting->issuer->name, 0, 1) }}
                                                                </div>
                                                                <span>{{ $meeting->issuer->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td>{{ $meeting->room->name ?? '-' }}</td>
                                                        <td class="text-center">
                                                            @php
                                                                // Convert string status to enum object
                                                                $statusEnum = App\Enums\InvestorStatus::from(
                                                                    $meeting->pivot->status,
                                                                );
                                                            @endphp
                                                            <span class="badge badge-{{ $statusEnum->color() }}">
                                                                {{ $statusEnum->label() }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="{{ route('admin.meetings.show', $meeting) }}"
                                                                class="btn btn-sm btn-primary">
                                                                {{ __('View') }}
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        {{ __('No meetings found for this investor.') }}
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar with Actions -->
            <div class="space-y-6">
                <!-- Actions Panel -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Actions') }}</h2>
                        <div class="space-y-3 mt-4">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary w-full">
                                <span> {{ __('Edit Account') }}</span>
                            </a>

                            @if ($user->hasRole(App\Enums\UserRole::ISSUER->value))
                                <a href="{{ route('admin.users.schedule', $user) }}" class="btn btn-accent w-full">
                                    <span> {{ __('Manage Schedule') }}</span>
                                </a>
                            @endif

                            <button type="button" class="btn btn-error w-full"
                                @click="$dispatch('open-modal', 'delete-user')">
                                <span> {{ __('Delete Account') }}</span>
                            </button>

                            <form method="POST" action="{{ route('admin.users.toggle-multiple-status') }}">
                                @method('PATCH')
                                @csrf
                                <input type="hidden" name="user_ids[]" value="{{ $user->id }}">
                                <input type="hidden" name="status" value="{{ $user->status ? '0' : '1' }}">
                                <button type="submit"
                                    class="btn {{ $user->status ? 'btn-warning' : 'btn-success' }} w-full">
                                    <span>{{ $user->status ? __('Deactivate Account') : __('Activate Account') }}</span>
                                </button>
                            </form>

                            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-outline w-full">
                                <span> {{ __('Back to List') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Account Details -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('Account Details') }}</h2>

                        <div class="mt-4 space-y-3">
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Created At') }}</div>
                                <div class="mt-1 text-sm">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Last Updated') }}</div>
                                <div class="mt-1 text-sm">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Last Login') }}</div>
                                <div class="mt-1 text-sm">
                                    @if ($user->last_login_at)
                                        {{ \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-base-content/50">{{ __('Never') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="card card-bordered bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-primary">{{ __('QR Code') }}</h2>

                        <div class="mt-4 flex flex-col items-center space-y-3">
                            @if($user->qr_code)
                                <div class="bg-white p-4 rounded-lg">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($user->qr_code) }}"
                                         alt="QR Code for {{ $user->name }}"
                                         class="w-32 h-32">
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-base-content/70">{{ __('User QR Code') }}</div>
                                    <div class="text-xs font-mono bg-base-200 px-2 py-1 rounded mt-1">{{ $user->qr_code }}</div>
                                </div>
                            @else
                                <div class="text-center text-base-content/50">
                                    <x-heroicon-s-qr-code class="size-16 mx-auto mb-2 opacity-50" />
                                    <div class="text-sm">{{ __('No QR code generated') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <x-modal name="delete-user" :show="false" focusable>
            <div class="p-6">
                <h3 class="text-lg font-medium text-error">{{ __('Delete Account') }}</h3>
                <p class="mt-3 text-sm text-base-content/70">
                    {{ __('Are you sure you want to delete this account? All of its resources and data will be permanently deleted.') }}
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn btn-ghost" x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </button>

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error">{{ __('Delete Account') }}</button>
                    </form>
                </div>
            </div>
        </x-modal>
    </section>
</x-app-layout>
