<div class="w-full">
    @if ($isLoading)
        <div class="flex flex-col items-center justify-center p-8">
            <span class="loading loading-spinner loading-lg text-primary mb-2"></span>
            <p class="text-base-content/70 text-sm">{{ __('Loading country information...') }}</p>
        </div>
    @elseif (!$countryCode)
        <div class="card card-dash bg-base-100 p-6">
            <div class="text-center py-6">
                {{-- <div class="avatar">
                    <div class="rounded-full w-16 h-16 bg-base-200 flex items-center justify-center">
                        <x-heroicon-o-globe-europe-africa class="h-10 w-10 text-primary/70" />
                    </div>
                </div> --}}
                <div class="flex justify-center">
                    <x-heroicon-o-globe-europe-africa class="size-16 text-primary/70" />
                </div>

                <h3 class="mt-4 text-lg font-medium text-base-content">{{ __('Country Information') }}</h3>
                <p class="mt-2 text-base text-base-content/70 max-w-sm mx-auto">
                    {{ __('Select a country on the map or from the list to view detailed information about users from that region.') }}
                </p>
                <p class="mt-4 text-base-content/50 text-sm">
                    <x-heroicon-o-cursor-arrow-rays class="inline-block h-4 w-4 mr-1" />
                    {{ __('Interactive visualization helps you understand your user distribution globally.') }}
                </p>
            </div>
        </div>
    @elseif ($userCount > 0)
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-lg font-semibold text-secondary">{{ $countryName }} ({{ $countryCode }})</h4>
                <span class="badge badge-lg badge-secondary">{{ $userCount }}
                    {{ $userCount > 1 ? __('users') : __('user') }}</span>
            </div>
            <div class="stats stats-vertical lg:stats-horizontal bg-base-200 shadow w-full">
                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <x-heroicon-s-user-group class="w-8 h-8" />
                    </div>
                    <div class="stat-title">{{ __('User Distribution') }}</div>
                    <div class="stat-value text-secondary">{{ $percentage }}%</div>
                    <div class="stat-desc">{{ __('of total platform users') }}</div>
                </div>
                <div class="stat">
                    <div class="stat-figure text-neutral">
                        <x-heroicon-s-building-office class="w-8 h-8" />
                    </div>
                    <div class="stat-title">{{ __('Organizations') }}</div>
                    <div class="stat-value text-neutral">{{ $organizationCount }}</div>
                    <div class="stat-desc">{{ __('registered from this country') }}</div>
                </div>
                <div class="stat">
                    <div class="stat-figure text-warning">
                        <x-heroicon-s-building-storefront class="w-8 h-8" />
                    </div>
                    <div class="stat-title">{{ __('Issuers') }}</div>
                    <div class="stat-value text-warning">{{ $issuerCount }}</div>
                    <div class="stat-desc">{{ $issuerCount > 0 ? ($issuerCount / $userCount * 100).'%' : '0%' }} {{ __('of country users') }}</div>
                </div>
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <x-heroicon-s-briefcase class="w-8 h-8" />
                    </div>
                    <div class="stat-title">{{ __('Investors') }}</div>
                    <div class="stat-value text-primary">{{ $investorCount }}</div>
                    <div class="stat-desc">{{ $investorCount > 0 ? ($investorCount / $userCount * 100).'%' : '0%' }} {{ __('of country users') }}</div>
                </div>
            </div>

            @if(count($topOrganizations) > 0)
            <div class="card bg-base-200 shadow-sm p-4">
                <h6 class="font-medium text-sm text-base-content mb-2">{{ __('Top Organizations') }}</h6>
                <div class="space-y-2">
                    @foreach($topOrganizations as $org)
                    <div class="flex items-center justify-between bg-base-100 p-2 rounded-lg">
                        <div>
                            <span class="font-medium">{{ $org['name'] }}</span>
                            @if($org['type'])
                            <span class="badge badge-sm badge-ghost ml-1">{{ $org['type'] }}</span>
                            @endif
                        </div>
                        <span class="badge badge-primary">{{ $org['count'] }} {{ $org['count'] > 1 ? __('users') : __('user') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if (count($users))
                <div class="flex items-center justify-between">
                    <h5 class="text-md font-medium text-secondary">{{ __('User List') }}</h5>
                    {{-- <span class="badge badge-neutral">{{ $users->total() }} {{ __('total') }}</span> --}}
                </div>

                <div class="card shadow-md overflow-x-auto">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Organization') }}</th>
                                <th class="text-center">{{ __('Role') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="hover">
                                    <td>{{ $user->name }} {{ $user->first_name }}</td>
                                    <td>{{ $user->organization->name ?? '-' }}</td>
                                    <td class="text-center">
                                        @foreach ($user->roles as $role)
                                            <span
                                                class="badge badge-sm {{ $role->name === 'issuer' ? 'badge-warning' : 'badge-primary' }}">
                                                {{ __($role->name) }}
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- <div class="mt-4">
                    {{ $users->links() }}
                </div> --}}
            @endif
        </div>
    @else
        <div class="card card-border bg-base-100 p-4">
            <div class="flex items-center justify-between">
                <h4 class="text-lg font-semibold text-secondary">{{ $countryName }} ({{ $countryCode }})</h4>
                <span class="badge badge-lg badge-ghost">{{ __('No data') }}</span>
            </div>
            <div class="divider my-2"></div>
            <div class="text-center py-4">
                <x-heroicon-o-information-circle class="mx-auto h-8 w-8 text-base-content/40" />
                <p class="mt-2 text-sm text-base-content/70">
                    {{ __('No users registered from this country.') }}
                </p>
            </div>
        </div>
    @endif
</div>
