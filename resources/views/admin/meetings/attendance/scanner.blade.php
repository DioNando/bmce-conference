<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.meetings.index') }}" class="text-primary">{{ __('Meetings') }}</a></li>
            <li><a href="{{ route('admin.meetings.show', $meeting) }}"
                    class="text-primary">{{ __('Meeting Details') }}</a></li>
            <li>{{ __('QR Code Scanner') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-primary">
                {{ __('QR Code Scanner') }}
            </h1>
        </div>

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title text-primary">{{ __('Scan Attendance QR Code') }}</h2>
                <p class="text-base-content/70">
                    {{ __('Scan the QR code of an investor to register their attendance to this meeting.') }}</p>
                <p class="text-xs text-base-content/60 mt-1">
                    {{ __('Note: There is a 5-second delay between scans to prevent accidental multiple scans.') }}</p>

                <div class="mt-6">
                    <!-- QR Code Scanner -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div id="reader" class="w-full h-auto border border-base-300 rounded-lg overflow-hidden">
                                <!-- Scanner loading indicator -->
                                <div id="scanner-loading" class="flex flex-col items-center justify-center h-64 bg-base-200">
                                    <span class="loading loading-spinner loading-lg text-primary mb-2"></span>
                                    <p class="text-base-content/70">{{ __('Initializing camera...') }}</p>
                                </div>
                            </div>
                            <div id="cooldown-indicator" class="mt-2 text-center hidden">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="loading loading-spinner loading-sm text-warning"></span>
                                    <span class="text-warning font-medium">{{ __('Scanner will be active in') }} <span
                                            id="cooldown-timer">5</span> {{ __('seconds') }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div id="result-container" class="hidden">
                                <div id="success-alert" class="alert alert-success mb-4 hidden">
                                    <x-heroicon-s-check-circle class="size-6" />
                                    <div>
                                        <h3 class="font-bold" id="success-message">{{ __('Attendance Recorded') }}</h3>
                                        <div id="investor-details"></div>
                                    </div>
                                </div>

                                <div id="warning-alert" class="alert alert-warning mb-4 hidden">
                                    <x-heroicon-s-exclamation-triangle class="size-6" />
                                    <div>
                                        <h3 class="font-bold" id="warning-message">{{ __('Already Checked In') }}</h3>
                                        <div id="duplicate-investor-details"></div>
                                    </div>
                                </div>

                                <div id="error-alert" class="alert alert-error mb-4 hidden">
                                    <x-heroicon-s-exclamation-circle class="size-6" />
                                    <div>
                                        <h3 class="font-bold" id="error-message">{{ __('Error') }}</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual QR Entry -->
                            <div class="mt-4">
                                <h3 class="text-lg font-medium">{{ __('Manual QR Entry') }}</h3>
                                <p class="text-sm text-base-content/70 mb-2">
                                    {{ __('If scanning doesn\'t work, you can enter the QR code manually:') }}</p>
                                <div class="w-full flex gap-3">
                                    <input type="text" id="manual-qr-input" class="input w-full"
                                        placeholder="Enter QR code">
                                    <button id="manual-submit" class="btn btn-primary">
                                        <x-heroicon-s-qr-code class="size-4" />
                                        {{ __('Verify') }}</button>
                                </div>
                            </div>

                            <!-- Last Scanned -->
                            <div class="mt-6">
                                <h3 class="text-lg font-medium">{{ __('Recent Check-ins') }}</h3>
                                <div id="recent-checkins" class="mt-2 overflow-auto max-h-40">
                                    <p class="text-base-content/70 text-sm">{{ __('No recent check-ins') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('admin.meetings.show', $meeting) }}"
                        class="btn btn-ghost">{{ __('Back to Meeting') }}</a>
                </div>
            </div>
        </div>

        <!-- Investors Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Present Investors Table -->
            <div class="card card-bordered bg-base-200 shadow-lg">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="card-title text-primary">{{ __('Present Investors') }}</h2>
                        <span id="present-count-badge" class="badge badge-success">{{ $presentInvestors->count() }} {{ __('checked in') }}</span>
                    </div>

                    <!-- Search bar for present investors -->
                    <div class="flex w-full mb-4">
                        <input type="text" id="present-investors-search" placeholder="{{ __('Search by name, organization...') }}" class="input input-bordered w-full" />
                    </div>

                    <div class="card bg-base-100 overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th class="cursor-pointer" data-sort="name">
                                        {{ __('Name') }}
                                        <span class="sort-icon">↕</span>
                                    </th>
                                    <th class="cursor-pointer" data-sort="organization">
                                        {{ __('Organization') }}
                                        <span class="sort-icon">↕</span>
                                    </th>
                                    <th class="cursor-pointer" data-sort="time">
                                        {{ __('Check-in Time') }}
                                        <span class="sort-icon">↕</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="present-investors-table">
                                @forelse ($presentInvestors as $investor)
                                    <tr>
                                        <td class="font-medium">
                                            {{ $investor->investor->name }} {{ $investor->investor->first_name }}
                                        </td>
                                        <td>{{ $investor->investor->organization->name ?? __('No Organization') }}</td>
                                        <td>{{ $investor->checked_in_at->format('H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">{{ __('No investors have checked in yet.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Absent Investors Table -->
            <div class="card card-bordered bg-base-200 shadow-lg">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="card-title text-primary">{{ __('Absent Investors') }}</h2>
                        <span id="absent-count-badge" class="badge badge-warning">{{ $absentInvestors->count() }} {{ __('not yet arrived') }}</span>
                    </div>

                    <!-- Search bar for absent investors -->
                    <div class="flex w-full mb-4">
                        <input type="text" id="absent-investors-search" placeholder="{{ __('Search by name, organization...') }}" class="input input-bordered w-full" />
                    </div>

                    <div class="card bg-base-100 overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th class="cursor-pointer" data-sort="name">
                                        {{ __('Name') }}
                                        <span class="sort-icon">↕</span>
                                    </th>
                                    <th class="cursor-pointer" data-sort="organization">
                                        {{ __('Organization') }}
                                        <span class="sort-icon">↕</span>
                                    </th>
                                    <th class="cursor-pointer" data-sort="status">
                                        {{ __('Status') }}
                                        <span class="sort-icon">↕</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="absent-investors-table">
                                @forelse ($absentInvestors as $investor)
                                    <tr>
                                        <td class="font-medium">
                                            {{ $investor->investor->name }} {{ $investor->investor->first_name }}
                                        </td>
                                        <td>{{ $investor->investor->organization->name ?? __('No Organization') }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $investor->status->color() }}">
                                                {{ __($investor->status->label()) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">{{ __('All investors have checked in!') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript Dependencies -->
    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const html5QrCode = new Html5Qrcode("reader");
                const meetingId = {{ $meeting->id }};
                const recentCheckins = document.getElementById('recent-checkins');
                let checkins = [];
                let scanningEnabled = true;
                let scanCooldown = false;
                const cooldownTime = 5; // Cooldown time in seconds
                let cooldownTimer = cooldownTime;
                let cooldownInterval;

                // Define configuration
                const config = {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0,
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    rememberLastUsedCamera: true,
                    showTorchButtonIfSupported: true,
                    showZoomSliderIfSupported: true,
                };

                // Success callback when QR is scanned
                const qrCodeSuccessCallback = (decodedText) => {
                    if (scanningEnabled && !scanCooldown) {
                        processQrCode(decodedText);
                    }
                };

                // Error callback for QR scanner
                const qrCodeErrorCallback = (error) => {
                    // Only log errors, don't show to user as these can be frequent camera adjustments
                    console.log(error);
                };

                // Start scanner
                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    qrCodeSuccessCallback,
                    qrCodeErrorCallback
                ).then(() => {
                    // Camera started successfully, hide the loading indicator
                    document.getElementById('scanner-loading').classList.add('hidden');
                }).catch((err) => {
                    // Camera failed to start, show error message in the loading indicator
                    document.getElementById('scanner-loading').innerHTML = `
                        <div class="text-error flex flex-col items-center justify-center">
                            <x-heroicon-s-exclamation-circle class="size-10 mb-2" />
                            <p class="font-medium">{{ __('Camera error') }}</p>
                            <p class="text-sm text-base-content/70">{{ __('Could not access the camera') }}</p>
                        </div>
                    `;
                    console.error('Error starting camera:', err);
                });

                // Manual submit
                document.getElementById('manual-submit').addEventListener('click', function() {
                    const qrCode = document.getElementById('manual-qr-input').value.trim();
                    if (qrCode && !scanCooldown) {
                        processQrCode(qrCode);
                        document.getElementById('manual-qr-input').value = '';
                    } else if (scanCooldown) {
                        // Optionally show a message that scanning is on cooldown
                        document.getElementById('manual-qr-input').classList.add('input-warning');
                        setTimeout(() => {
                            document.getElementById('manual-qr-input').classList.remove(
                                'input-warning');
                        }, 1000);
                    }
                });

                // Start cooldown timer
                function startCooldown() {
                    scanCooldown = true;
                    cooldownTimer = cooldownTime;
                    document.getElementById('cooldown-timer').textContent = cooldownTimer;
                    document.getElementById('cooldown-indicator').classList.remove('hidden');

                    // Disable scanning temporarily
                    scanningEnabled = false;

                    // Start cooldown countdown
                    cooldownInterval = setInterval(() => {
                        cooldownTimer--;
                        document.getElementById('cooldown-timer').textContent = cooldownTimer;

                        if (cooldownTimer <= 0) {
                            // Re-enable scanning
                            clearInterval(cooldownInterval);
                            scanCooldown = false;
                            scanningEnabled = true;
                            document.getElementById('cooldown-indicator').classList.add('hidden');
                        }
                    }, 1000);
                }

                // Process QR code
                function processQrCode(qrCode) {
                    // Prevent processing if on cooldown
                    if (scanCooldown) return;

                    // Start cooldown
                    startCooldown();

                    // Show loading state
                    document.getElementById('result-container').classList.remove('hidden');
                    document.getElementById('success-alert').classList.add('hidden');
                    document.getElementById('warning-alert').classList.add('hidden');
                    document.getElementById('error-alert').classList.add('hidden');

                    // Call API to verify QR code
                    fetch('{{ route('admin.attendance.verify') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                qr_code: qrCode,
                                meeting_id: meetingId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                document.getElementById('success-alert').classList.remove('hidden');
                                document.getElementById('error-alert').classList.add('hidden');
                                document.getElementById('success-message').textContent = data.message;

                                // Display investor details
                                const investor = data.investor;
                                const details = `
                            <p class="text-sm"><strong>${investor.name}</strong></p>
                            ${investor.organization ? `<p class="text-xs">${investor.organization}</p>` : ''}
                            <p class="text-xs">${data.checked_in_at}</p>
                            <p class="text-xs mt-2 italic">{{ __('Updating tables...') }}</p>
                        `;
                                document.getElementById('investor-details').innerHTML = details;

                                // Add to recent checkins
                                checkins.unshift({
                                    name: investor.name,
                                    organization: investor.organization || '',
                                    time: data.checked_in_at
                                });
                                updateRecentCheckins();

                                // Set a timeout to update tables after successful check-in
                                setTimeout(() => {
                                    refreshInvestorTables();
                                }, 2000); // Update tables after 2 seconds
                            } else {
                                // Handle already checked in case
                                if (data.already_checked_in && data.investor) {
                                    document.getElementById('success-alert').classList.add('hidden');
                                    document.getElementById('error-alert').classList.add('hidden');
                                    document.getElementById('warning-alert').classList.remove('hidden');
                                    document.getElementById('warning-message').textContent = data.message;

                                    // Display duplicate investor details
                                    const investor = data.investor;
                                    const details = `
                                <p class="text-sm"><strong>${investor.name}</strong></p>
                                ${investor.organization ? `<p class="text-xs">${investor.organization}</p>` : ''}
                                <p class="text-xs">{{ __('Previous check-in') }}: ${data.checked_in_at}</p>
                            `;
                                    document.getElementById('duplicate-investor-details').innerHTML = details;
                                } else {
                                    // Show regular error message
                                    document.getElementById('success-alert').classList.add('hidden');
                                    document.getElementById('warning-alert').classList.add('hidden');
                                    document.getElementById('error-alert').classList.remove('hidden');
                                    document.getElementById('error-message').textContent = data.message;
                                }
                            }
                        })
                        .catch(error => {
                            // Show error message
                            document.getElementById('success-alert').classList.add('hidden');
                            document.getElementById('warning-alert').classList.add('hidden');
                            document.getElementById('error-alert').classList.remove('hidden');
                            document.getElementById('error-message').textContent =
                                '{{ __('An error occurred while processing the QR code.') }}';
                            console.error('Error:', error);
                        });
                }

                // Update recent checkins
                function updateRecentCheckins() {
                    if (checkins.length === 0) {
                        recentCheckins.innerHTML =
                            `<p class="text-base-content/70 text-sm">{{ __('No recent check-ins') }}</p>`;
                        return;
                    }

                    recentCheckins.innerHTML = '';
                    checkins.slice(0, 5).forEach(checkin => {
                        const checkinEl = document.createElement('div');
                        checkinEl.className = 'py-2 border-b border-base-200 last:border-0';
                        checkinEl.innerHTML = `
                        <p class="text-sm font-medium">${checkin.name}</p>
                        ${checkin.organization ? `<p class="text-xs text-base-content/70">${checkin.organization}</p>` : ''}
                        <p class="text-xs text-base-content/70">${checkin.time}</p>
                    `;
                        recentCheckins.appendChild(checkinEl);
                    });
                }

                // Function to refresh investor tables via AJAX
                function refreshInvestorTables() {
                    fetch('{{ route("admin.attendance.tables", $meeting) }}')
                        .then(response => response.json())
                        .then(data => {
                            // Store data globally for filtering/sorting
                            window.presentInvestorsData = data.present_investors;
                            window.absentInvestorsData = data.absent_investors;

                            // Update present investors table
                            updatePresentInvestorsTable(data.present_investors, data.present_count);

                            // Update absent investors table
                            updateAbsentInvestorsTable(data.absent_investors, data.absent_count);
                        })
                        .catch(error => {
                            console.error('Error fetching investor tables:', error);
                        });
                }

                // Update present investors table
                function updatePresentInvestorsTable(investors, count) {
                    const tableBody = document.getElementById('present-investors-table');
                    const countBadge = document.getElementById('present-count-badge');

                    countBadge.textContent = `${count} {{ __('checked in') }}`;

                    if (investors.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center py-4">{{ __('No investors have checked in yet.') }}</td>
                            </tr>
                        `;
                        return;
                    }

                    tableBody.innerHTML = '';
                    investors.forEach(investor => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="font-medium">${investor.name}</td>
                            <td>${investor.organization || '{{ __("No Organization") }}'}</td>
                            <td>${investor.checked_in_at}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                }

                // Update absent investors table
                function updateAbsentInvestorsTable(investors, count) {
                    const tableBody = document.getElementById('absent-investors-table');
                    const countBadge = document.getElementById('absent-count-badge');

                    countBadge.textContent = `${count} {{ __('not yet arrived') }}`;

                    if (investors.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center py-4">{{ __('All investors have checked in!') }}</td>
                            </tr>
                        `;
                        return;
                    }

                    tableBody.innerHTML = '';
                    investors.forEach(investor => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="font-medium">${investor.name}</td>
                            <td>${investor.organization || '{{ __("No Organization") }}'}</td>
                            <td><span class="badge badge-${investor.status.color}">${investor.status.label}</span></td>
                        `;
                        tableBody.appendChild(row);
                    });
                }

                // Initial load of tables
                refreshInvestorTables();

                // Setup search and sorting functionality
                setupTableFunctionality();

                function setupTableFunctionality() {
                    // Present investors search
                    document.getElementById('present-investors-search').addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        if (!window.presentInvestorsData) return;

                        const filteredData = window.presentInvestorsData.filter(investor =>
                            investor.name.toLowerCase().includes(searchTerm) ||
                            (investor.organization && investor.organization.toLowerCase().includes(searchTerm))
                        );

                        updatePresentInvestorsTable(filteredData, filteredData.length);
                    });

                    // Absent investors search
                    document.getElementById('absent-investors-search').addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        if (!window.absentInvestorsData) return;

                        const filteredData = window.absentInvestorsData.filter(investor =>
                            investor.name.toLowerCase().includes(searchTerm) ||
                            (investor.organization && investor.organization.toLowerCase().includes(searchTerm))
                        );

                        updateAbsentInvestorsTable(filteredData, filteredData.length);
                    });

                    // Setup sorting for present investors table
                    const presentTableHeaders = document.querySelectorAll('#present-investors-table').length > 0
                        ? document.querySelector('#present-investors-table').closest('table').querySelectorAll('th[data-sort]')
                        : [];

                    presentTableHeaders.forEach(header => {
                        header.addEventListener('click', function() {
                            const sortKey = this.getAttribute('data-sort');
                            const currentOrder = this.getAttribute('data-order') || 'asc';
                            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                            // Reset all headers
                            presentTableHeaders.forEach(h => {
                                h.setAttribute('data-order', '');
                                h.querySelector('.sort-icon').textContent = '↕';
                            });

                            // Set current header
                            this.setAttribute('data-order', newOrder);
                            this.querySelector('.sort-icon').textContent = newOrder === 'asc' ? '↑' : '↓';

                            if (!window.presentInvestorsData) return;

                            // Sort data
                            const sortedData = [...window.presentInvestorsData].sort((a, b) => {
                                let valueA, valueB;

                                if (sortKey === 'name') {
                                    valueA = a.name.toLowerCase();
                                    valueB = b.name.toLowerCase();
                                } else if (sortKey === 'organization') {
                                    valueA = (a.organization || '').toLowerCase();
                                    valueB = (b.organization || '').toLowerCase();
                                } else if (sortKey === 'time') {
                                    valueA = a.checked_in_at;
                                    valueB = b.checked_in_at;
                                }

                                if (valueA < valueB) return newOrder === 'asc' ? -1 : 1;
                                if (valueA > valueB) return newOrder === 'asc' ? 1 : -1;
                                return 0;
                            });

                            updatePresentInvestorsTable(sortedData, sortedData.length);
                        });
                    });

                    // Setup sorting for absent investors table
                    const absentTableHeaders = document.querySelectorAll('#absent-investors-table').length > 0
                        ? document.querySelector('#absent-investors-table').closest('table').querySelectorAll('th[data-sort]')
                        : [];

                    absentTableHeaders.forEach(header => {
                        header.addEventListener('click', function() {
                            const sortKey = this.getAttribute('data-sort');
                            const currentOrder = this.getAttribute('data-order') || 'asc';
                            const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                            // Reset all headers
                            absentTableHeaders.forEach(h => {
                                h.setAttribute('data-order', '');
                                h.querySelector('.sort-icon').textContent = '↕';
                            });

                            // Set current header
                            this.setAttribute('data-order', newOrder);
                            this.querySelector('.sort-icon').textContent = newOrder === 'asc' ? '↑' : '↓';

                            if (!window.absentInvestorsData) return;

                            // Sort data
                            const sortedData = [...window.absentInvestorsData].sort((a, b) => {
                                let valueA, valueB;

                                if (sortKey === 'name') {
                                    valueA = a.name.toLowerCase();
                                    valueB = b.name.toLowerCase();
                                } else if (sortKey === 'organization') {
                                    valueA = (a.organization || '').toLowerCase();
                                    valueB = (b.organization || '').toLowerCase();
                                } else if (sortKey === 'status') {
                                    valueA = a.status.label.toLowerCase();
                                    valueB = b.status.label.toLowerCase();
                                }

                                if (valueA < valueB) return newOrder === 'asc' ? -1 : 1;
                                if (valueA > valueB) return newOrder === 'asc' ? 1 : -1;
                                return 0;
                            });

                            updateAbsentInvestorsTable(sortedData, sortedData.length);
                        });
                    });
                }

                // Stop scanner when user leaves the page
                window.addEventListener('beforeunload', function() {
                    // Only stop if scanner is initialized
                    if (html5QrCode) {
                        html5QrCode.stop().then(() => {
                            console.log('Scanner stopped before page unload');
                        }).catch(err => {
                            console.error('Failed to stop scanner:', err);
                        });
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
