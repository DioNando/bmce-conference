<x-app-layout>
    <div class="flex items-center justify-between mb-4">
        <div class="breadcrumbs text-sm">
            <ul>
                <li class="text-primary">{{ __('Dashboard') }}</li>
            </ul>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.scanner') }}" class="btn btn-warning">
                <x-heroicon-s-qr-code class="size-4" />
                <span class="hidden lg:block">{{ __('Scan QR Code') }}</span>
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <x-heroicon-s-user-plus class="size-4" />
                <span class="hidden lg:block">{{ __('Add Account') }}</span>
            </a>
            <a href="{{ route('admin.meetings.create') }}" class="btn btn-primary">
                <x-heroicon-s-calendar class="size-4" />
                <span class="hidden lg:block">{{ __('Create Meeting') }}</span>
            </a>
        </div>
    </div>
    <section class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center gap-6">

            <!-- User Admin Info Card -->
            <div class="w-fit card card-border  bg-base-100 shadow-lg p-6">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center space-x-4">

                        <div class="flex-shrink-0">
                            @if (Auth::user()->profile_photo_path)
                                <img class="h-16 w-16 rounded-full object-cover"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                            @else
                                <div
                                    class="h-16 w-16 rounded-full bg-neutral/10 flex items-center justify-center text-neutral text-2xl font-mono font-semibold">
                                    {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-base-content">
                                {{ Auth::user()->first_name . ' ' . Auth::user()->name }}</h4>
                            <p class="text-base-content/80">{{ Auth::user()->email }}</p>
                            {{-- <p class="text-sm text-base-content/60">{{ __('Last Login') }}:
                                {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : __('First Login') }}
                            </p> --}}
                        </div>
                    </div>
                    <span class="badge badge-neutral badge-lg">
                        {{ __('Administrator') }}
                    </span>
                </div>
            </div>

            <!-- Dashboard Quick Actions -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users.index') }}"
                    class="card bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-primary">{{ __('Manage Users') }}</h4>
                            <x-heroicon-s-users class="w-8 h-8 text-primary/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">
                            {{ __('View, add, edit and manage all user accounts') }}</p>
                    </div>
                </a>

                <a href="{{ route('admin.organizations.index') }}"
                    class="card bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-secondary">{{ __('Organizations') }}</h4>
                            <x-heroicon-s-building-office class="w-8 h-8 text-secondary/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">{{ __('Manage organizations and their details') }}
                        </p>
                    </div>
                </a>

                <a href="{{ route('admin.meetings.index') }}"
                    class="card bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-accent">{{ __('Meetings') }}</h4>
                            <x-heroicon-s-calendar class="w-8 h-8 text-accent/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">{{ __('Schedule and manage meetings') }}</p>
                    </div>
                </a>

                <a href="{{ route('admin.rooms.index') }}"
                    class="card bg-base-100 hover:bg-base-200 transition-colors">
                    <div class="card-body">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xl font-semibold text-success">{{ __('Manage Rooms') }}</h4>
                            <x-heroicon-s-building-library class="w-8 h-8 text-success/80" />
                        </div>
                        <p class="mt-2 text-sm text-base-content/70">{{ __('View and manage meeting rooms') }}</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section utilisateurs (Émetteurs et Investisseurs) -->
        <div class="flex justify-between items-center">
            <h3 class="flex items-center gap-2 text-2xl font-bold text-primary">
                <x-heroicon-s-chart-bar class="size-6" />
                {{ __('Platform Statistics') }}
            </h3>
        </div>

        <!-- Key Platform Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="card bg-gradient-to-br from-primary/5 to-primary/10 border border-primary/20 shadow-lg">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-primary/80 font-medium">{{ __('Total Users') }}</p>
                            <p class="text-3xl font-bold text-primary">{{ number_format($stats['totalUsers']) }}</p>
                            <p class="text-sm text-primary/60">
                                <span class="text-success">+{{ $stats['activityMetrics']['new_users_24h'] }}</span>
                                {{ __('today') }}
                            </p>
                        </div>
                        <div class="stat-figure text-primary/40">
                            <x-heroicon-s-user-group class="size-12" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Organizations -->
            <div class="card bg-gradient-to-br from-secondary/5 to-secondary/10 border border-secondary/20 shadow-lg">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-secondary/80 font-medium">{{ __('Organizations') }}</p>
                            <p class="text-3xl font-bold text-secondary">
                                {{ number_format($stats['totalOrganizations']) }}</p>
                            <p class="text-sm text-secondary/60">{{ __('Registered companies') }}</p>
                        </div>
                        <div class="stat-figure text-secondary/40">
                            <x-heroicon-s-building-office class="size-12" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Meetings -->
            <div class="card bg-gradient-to-br from-accent/5 to-accent/10 border border-accent/20 shadow-lg">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-accent/80 font-medium">{{ __('Total Meetings') }}</p>
                            <p class="text-3xl font-bold text-accent">{{ number_format($stats['totalMeetings']) }}</p>
                            <p class="text-sm text-accent/60">
                                <span class="text-success">+{{ $stats['activityMetrics']['new_meetings_24h'] }}</span>
                                {{ __('today') }}
                            </p>
                        </div>
                        <div class="stat-figure text-accent/40">
                            <x-heroicon-s-calendar-days class="size-12" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Utilization -->
            <div class="card bg-gradient-to-br from-success/5 to-success/10 border border-success/20 shadow-lg">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-success/80 font-medium">{{ __('Platform Efficiency') }}</p>
                            <p class="text-3xl font-bold text-success">
                                {{ $stats['timeSlotStats']['utilization_rate'] }}%</p>
                            <p class="text-sm text-success/60">
                                {{ $stats['timeSlotStats']['booked'] }}/{{ $stats['timeSlotStats']['total'] }}
                                {{ __('slots booked') }}</p>
                        </div>
                        <div class="stat-figure text-success/40">
                            <x-heroicon-s-chart-pie class="size-12" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques détaillées par rôle -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Issuers Stats -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-warning flex items-center gap-2">
                        <x-heroicon-s-building-storefront class="size-5" />
                        {{ __('Issuers') }}
                    </h3>
                    <div>
                        <div class="stat p-0">
                            <div class="stat-value text-warning">{{ number_format($stats['issuersCount']) }}</div>
                            <div class="stat-desc">{{ __('Companies presenting') }}</div>
                        </div>
                    </div>
                    <div class="divider my-2"></div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm">{{ __('Organizations with issuers') }}</span>
                            <span class="font-semibold">{{ $stats['organizationStats']['with_issuers'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Investors Stats -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-primary flex items-center gap-2">
                        <x-heroicon-s-briefcase class="size-5" />
                        {{ __('Investors') }}
                    </h3>
                    <div>
                        <div class="stat p-0">
                            <div class="stat-value text-primary">{{ number_format($stats['investorsCount']) }}</div>
                            <div class="stat-desc">{{ __('Investment professionals') }}</div>
                        </div>
                    </div>
                    <div class="divider my-2"></div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm">{{ __('Organizations with investors') }}</span>
                            <span class="font-semibold">{{ $stats['organizationStats']['with_investors'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Stats -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-error flex items-center gap-2">
                        <x-heroicon-s-shield-check class="size-5" />
                        {{ __('System') }}
                    </h3>
                    <div>
                        <div class="stat p-0">
                            <div class="stat-value text-error">{{ number_format($stats['adminsCount']) }}</div>
                            <div class="stat-desc">{{ __('Administrators') }}</div>
                        </div>
                    </div>
                    <div class="divider my-2"></div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm">{{ __('Total rooms') }}</span>
                            <span class="font-semibold">{{ $stats['rooms'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meetings Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Meeting Status Breakdown -->
            <div class="card  bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-accent flex items-center gap-2">
                        <x-heroicon-s-calendar class="size-5" />
                        {{ __('Meetings Status') }}
                    </h3>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="bg-info/10 card border border-info/20 p-3">
                            <div class="stat-title text-xs">{{ __('Upcoming') }}</div>
                            <div class="stat-value text-lg text-info">{{ $stats['meetingsByStatus']['upcoming'] }}
                            </div>
                        </div>
                        <div class="bg-primary/10 card border border-primary/20 p-3">
                            <div class="stat-title text-xs">{{ __('Scheduled') }}</div>
                            <div class="stat-value text-lg text-primary">{{ $stats['meetingsByStatus']['scheduled'] }}
                            </div>
                        </div>
                        <div class="bg-success/10 card border border-success/20 p-3">
                            <div class="stat-title text-xs">{{ __('Completed') }}</div>
                            <div class="stat-value text-lg text-success">{{ $stats['meetingsByStatus']['completed'] }}
                            </div>
                        </div>
                        <div class="bg-error/10 card border border-error/20 p-3">
                            <div class="stat-title text-xs">{{ __('Cancelled') }}</div>
                            <div class="stat-value text-lg text-error">{{ $stats['meetingsByStatus']['cancelled'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participation Rates -->
            <div class="card  bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-secondary flex items-center gap-2">
                        <x-heroicon-s-chart-bar-square class="size-5" />
                        {{ __('Participation Analytics') }}
                    </h3>
                    <div class="space-y-4 mt-4">
                        <!-- Confirmation Rate -->
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ __('Confirmation Rate') }}</span>
                                <span
                                    class="font-semibold">{{ $stats['participationRates']['confirmation_rate'] }}%</span>
                            </div>
                            <progress class="progress progress-success"
                                value="{{ $stats['participationRates']['confirmation_rate'] }}"
                                max="100"></progress>
                        </div>

                        <!-- Attendance Rate -->
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ __('Attendance Rate') }}</span>
                                <span
                                    class="font-semibold">{{ $stats['participationRates']['attendance_rate'] }}%</span>
                            </div>
                            <progress class="progress progress-primary"
                                value="{{ $stats['participationRates']['attendance_rate'] }}"
                                max="100"></progress>
                        </div>

                        <!-- Refusal Rate -->
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ __('Refusal Rate') }}</span>
                                <span class="font-semibold">{{ $stats['participationRates']['refusal_rate'] }}%</span>
                            </div>
                            <progress class="progress progress-error"
                                value="{{ $stats['participationRates']['refusal_rate'] }}" max="100"></progress>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions & Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Questions Analytics -->
            <div class="card  bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-info flex items-center gap-2">
                        <x-heroicon-s-chat-bubble-left-right class="size-5" />
                        {{ __('Questions & Engagement') }}
                    </h3>
                    <div class="stats bg-transparent p-0 mt-4">
                        <div class="stat">
                            <div class="stat-title">{{ __('Total Questions') }}</div>
                            <div class="stat-value text-info">{{ number_format($stats['questionStats']['total']) }}
                            </div>
                            <div class="stat-desc">
                                <span
                                    class="text-success">+{{ $stats['activityMetrics']['new_questions_24h'] }}</span>
                                {{ __('today') }}
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="card border border-success/20 bg-success/10 p-3">
                            <div class="stat-title text-xs">{{ __('Answered') }}</div>
                            <div class="stat-value text-sm text-success">{{ $stats['questionStats']['answered'] }}
                            </div>
                        </div>
                        <div class="card border border-warning/20 bg-warning/10 p-3">
                            <div class="stat-title text-xs">{{ __('Pending') }}</div>
                            <div class="stat-value text-sm text-warning">{{ $stats['questionStats']['unanswered'] }}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ __('Response Rate') }}</span>
                            <span class="font-semibold">{{ $stats['questionStats']['response_rate'] }}%</span>
                        </div>
                        <progress class="progress progress-info"
                            value="{{ $stats['questionStats']['response_rate'] }}" max="100"></progress>
                    </div> --}}
                </div>
            </div>

            <!-- Activity Dashboard -->
            <div class="card  bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-success flex items-center gap-2">
                        <x-heroicon-s-bolt class="size-5" />
                        {{ __('Recent Activity (24h)') }}
                    </h3>
                    <div class="space-y-4 mt-4">
                        <div class="flex items-center justify-between p-3 bg-primary/10 rounded-lg">
                            <div class="flex items-center gap-3">
                                <x-heroicon-s-user-plus class="size-5 text-primary" />
                                <span class="text-sm">{{ __('New Users') }}</span>
                            </div>
                            <span
                                class="text-lg font-bold text-primary">{{ $stats['activityMetrics']['new_users_24h'] }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-secondary/10 rounded-lg">
                            <div class="flex items-center gap-3">
                                <x-heroicon-s-calendar-days class="size-5 text-secondary" />
                                <span class="text-sm">{{ __('New Meetings') }}</span>
                            </div>
                            <span
                                class="text-lg font-bold text-secondary">{{ $stats['activityMetrics']['new_meetings_24h'] }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-accent/10 rounded-lg">
                            <div class="flex items-center gap-3">
                                <x-heroicon-s-chat-bubble-oval-left class="size-5 text-accent" />
                                <span class="text-sm">{{ __('New Questions') }}</span>
                            </div>
                            <span
                                class="text-lg font-bold text-accent">{{ $stats['activityMetrics']['new_questions_24h'] }}</span>
                        </div>

                        {{-- <div class="flex items-center justify-between p-3 bg-success/10 rounded-lg">
                            <div class="flex items-center gap-3">
                                <x-heroicon-s-arrow-right-on-rectangle class="size-5 text-success" />
                                <span class="text-sm">{{ __('Active Logins') }}</span>
                            </div>
                            <span class="text-lg font-bold text-success">{{ $stats['activityMetrics']['logins_24h'] }}</span>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Issuers -->
            <div class="card  bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-warning flex items-center gap-2">
                        <x-heroicon-s-trophy class="size-5" />
                        {{ __('Most Active Issuers') }}
                    </h3>
                    <div class="space-y-3 mt-4">
                        @forelse($stats['topIssuers'] as $index => $issuer)
                            <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="badge badge-warning rounded-full">{{ $index + 1 }}</div>
                                    <div>
                                        <div class="font-semibold">{{ $issuer->first_name }} {{ $issuer->name }}
                                        </div>
                                        <div class="text-xs text-base-content/70">
                                            {{ $issuer->organization->name ?? __('No organization') }}</div>
                                    </div>
                                </div>
                                <div class="badge badge-outline">{{ $issuer->meetings_count }} {{ __('meetings') }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-base-content/60 py-4">
                                {{ __('No data available') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Investors -->
            <div class="card  bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-primary flex items-center gap-2">
                        <x-heroicon-s-star class="size-5" />
                        {{ __('Most Active Investors') }}
                    </h3>
                    <div class="space-y-3 mt-4">
                        @forelse($stats['topInvestors'] as $index => $investor)
                            <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="badge badge-primary rounded-full">{{ $index + 1 }}</div>
                                    <div>
                                        <div class="font-semibold">{{ $investor->first_name }}
                                            {{ $investor->name }}</div>
                                        <div class="text-xs text-base-content/70">
                                            {{ $investor->organization->name ?? __('No organization') }}</div>
                                    </div>
                                </div>
                                <div class="badge badge-outline">{{ $investor->meetings_count }}
                                    {{ __('meetings') }}</div>
                            </div>
                        @empty
                            <div class="text-center text-base-content/60 py-4">
                                {{ __('No data available') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Evolution Chart -->
        {{-- <div class="card  bg-base-100 shadow-lg">
            <div class="card-body">
                <h3 class="card-title text-info flex items-center gap-2">
                    <x-heroicon-s-chart-bar class="size-5" />
                    {{ __('Platform Growth (Last 6 Months)') }}
                </h3>
                <div class="overflow-x-auto mt-4">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Month') }}</th>
                                <th class="text-center">{{ __('New Users') }}</th>
                                <th class="text-center">{{ __('New Meetings') }}</th>
                                <th class="text-center">{{ __('New Organizations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stats['monthlyEvolution'] as $month)
                                <tr>
                                    <td class="font-medium">{{ $month['month'] }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $month['new_users'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ $month['new_meetings'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-accent">{{ $month['new_organizations'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}

        <!-- Charts Section -->
        {{-- <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="card  bg-base-100 shadow-lg p-4">
                <canvas id="meetingStatusChart" data-stats='{{ json_encode($meetingStatusStats) }}'></canvas>
            </div>
            <div class="card  bg-base-100 shadow-lg p-4">
                <canvas id="organizationTypeChart" data-stats='{{ json_encode($organizationTypeStats) }}'></canvas>
            </div>
            <div class="card  bg-base-100 shadow-lg p-4 mt-6">
                <canvas id="registrationChart" data-stats='{{ json_encode($registrationEvolution) }}'></canvas>
            </div>
        </div> --}}

        <!-- Carte des utilisateurs par pays -->
        <div class="flex justify-between items-center w-full">
            <x-admin.world-map :usersByCountry="$usersByCountry" :totalUsers="$stats['totalUsers']" />
        </div>
    </section>
</x-app-layout>
