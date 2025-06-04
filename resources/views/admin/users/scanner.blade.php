<x-app-layout>
    <div class="breadcrumbs text-sm mb-4">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="text-primary">{{ __('Dashboard') }}</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="text-primary">{{ __('Accounts') }}</a></li>
            <li>{{ __('QR Code Scanner') }}</li>
        </ul>
    </div>

    <section class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-primary">
                {{ __('User QR Code Scanner') }}
            </h1>
        </div>

        <div class="card card-bordered bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title text-primary">{{ __('Scan User QR Code') }}</h2>
                <p class="text-base-content/70">
                    {{ __('Scan the QR code of a user to view their information and associated meetings.') }}
                </p>
                <p class="text-xs text-base-content/60 mt-1">
                    {{ __('Note: There is a 5-second delay between scans to prevent accidental multiple scans.') }}
                </p>

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
                                        <h3 class="font-bold" id="success-message">{{ __('User Found') }}</h3>
                                        <div id="user-details"></div>
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
                                    {{ __('If scanning doesn\'t work, you can enter the QR code manually:') }}
                                </p>
                                <div class="w-full flex gap-3">
                                    <input type="text" id="manual-qr-input" class="input w-full"
                                        placeholder="Enter QR code">
                                    <button id="manual-submit" class="btn btn-primary">
                                        <x-heroicon-s-qr-code class="size-4" />
                                        {{ __('Search') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Last Scanned -->
                            <div class="mt-6">
                                <h3 class="text-lg font-medium">{{ __('Recent Scans') }}</h3>
                                <div id="recent-scans" class="mt-2 overflow-auto max-h-40">
                                    <p class="text-base-content/70 text-sm">{{ __('No recent scans') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('admin.users.index') }}"
                        class="btn btn-ghost">{{ __('Back to Users') }}</a>
                </div>
            </div>
        </div>

        <!-- User Information Display -->
        <div id="user-info-section" class="hidden">
            <div class="card card-bordered bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-primary">{{ __('User Information') }}</h2>
                    <div id="user-info-content">
                        <!-- User details will be populated here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- User Meetings Display -->
        <div id="user-meetings-section" class="hidden">
            <div class="card card-bordered bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-primary">{{ __('User Meetings') }}</h2>
                    <div id="user-meetings-content">
                        <!-- User meetings will be populated here -->
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
                const recentScans = document.getElementById('recent-scans');
                let scans = [];
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
                    console.log(error);
                };

                // Start scanner
                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    qrCodeSuccessCallback,
                    qrCodeErrorCallback
                ).then(() => {
                    document.getElementById('scanner-loading').classList.add('hidden');
                }).catch((err) => {
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
                        document.getElementById('manual-qr-input').classList.add('input-warning');
                        setTimeout(() => {
                            document.getElementById('manual-qr-input').classList.remove('input-warning');
                        }, 1000);
                    }
                });

                // Start cooldown timer
                function startCooldown() {
                    scanCooldown = true;
                    cooldownTimer = cooldownTime;
                    document.getElementById('cooldown-timer').textContent = cooldownTimer;
                    document.getElementById('cooldown-indicator').classList.remove('hidden');

                    scanningEnabled = false;

                    cooldownInterval = setInterval(() => {
                        cooldownTimer--;
                        document.getElementById('cooldown-timer').textContent = cooldownTimer;

                        if (cooldownTimer <= 0) {
                            clearInterval(cooldownInterval);
                            scanCooldown = false;
                            scanningEnabled = true;
                            document.getElementById('cooldown-indicator').classList.add('hidden');
                        }
                    }, 1000);
                }

                // Process QR code
                function processQrCode(qrCode) {
                    if (scanCooldown) return;

                    startCooldown();

                    document.getElementById('result-container').classList.remove('hidden');
                    document.getElementById('success-alert').classList.add('hidden');
                    document.getElementById('error-alert').classList.add('hidden');

                    // Call API to find user by QR code
                    fetch('{{ route('admin.users.verify-qr') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                qr_code: qrCode
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('success-alert').classList.remove('hidden');
                                document.getElementById('error-alert').classList.add('hidden');
                                document.getElementById('success-message').textContent = data.message;

                                const user = data.user;
                                const details = `
                            <p class="text-sm"><strong>${user.name} ${user.first_name}</strong></p>
                            <p class="text-xs">${user.email}</p>
                            ${user.organization ? `<p class="text-xs">${user.organization}</p>` : ''}
                            <p class="text-xs mt-2 italic">{{ __('Loading user details...') }}</p>
                        `;
                                document.getElementById('user-details').innerHTML = details;

                                // Add to recent scans
                                scans.unshift({
                                    name: user.name + ' ' + user.first_name,
                                    email: user.email,
                                    organization: user.organization || '',
                                    time: new Date().toLocaleTimeString()
                                });
                                updateRecentScans();

                                // Load user details
                                loadUserDetails(data.user.id);
                            } else {
                                document.getElementById('success-alert').classList.add('hidden');
                                document.getElementById('error-alert').classList.remove('hidden');
                                document.getElementById('error-message').textContent = data.message;

                                // Hide user sections
                                document.getElementById('user-info-section').classList.add('hidden');
                                document.getElementById('user-meetings-section').classList.add('hidden');
                            }
                        })
                        .catch(error => {
                            document.getElementById('success-alert').classList.add('hidden');
                            document.getElementById('error-alert').classList.remove('hidden');
                            document.getElementById('error-message').textContent =
                                '{{ __('An error occurred while processing the QR code.') }}';
                            console.error('Error:', error);
                        });
                }

                // Load user details
                function loadUserDetails(userId) {
                    fetch(`{{ route('admin.users.index') }}/${userId}/details`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                displayUserInfo(data.user);
                                displayUserMeetings(data.meetings);
                            }
                        })
                        .catch(error => {
                            console.error('Error loading user details:', error);
                        });
                }

                // Display user information
                function displayUserInfo(user) {
                    const userInfoContent = document.getElementById('user-info-content');
                    userInfoContent.innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Name') }}</div>
                                <div class="mt-1 text-base">${user.name} ${user.first_name}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Email') }}</div>
                                <div class="mt-1 text-base">${user.email}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Phone') }}</div>
                                <div class="mt-1 text-base">${user.phone || '-'}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Position') }}</div>
                                <div class="mt-1 text-base">${user.position || '-'}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Role') }}</div>
                                <div class="mt-1">
                                    <span class="badge ${user.role === 'issuer' ? 'badge-warning' : 'badge-primary'}">${user.role_label}</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-base-content/70">{{ __('Organization') }}</div>
                                <div class="mt-1 text-base">${user.organization || '-'}</div>
                            </div>
                        </div>
                        <div class="mt-4 flex gap-3">
                            <a href="/admin/users/${user.id}" class="btn btn-primary btn-sm">
                                {{ __('View Full Profile') }}
                            </a>
                            <a href="/admin/users/${user.id}/edit" class="btn btn-outline btn-sm">
                                {{ __('Edit User') }}
                            </a>
                        </div>
                    `;
                    document.getElementById('user-info-section').classList.remove('hidden');
                }

                // Display user meetings
                function displayUserMeetings(meetings) {
                    const userMeetingsContent = document.getElementById('user-meetings-content');

                    if (meetings.length === 0) {
                        userMeetingsContent.innerHTML = `
                            <div class="alert alert-info">
                                {{ __('No meetings found for this user.') }}
                            </div>
                        `;
                    } else {
                        userMeetingsContent.innerHTML = `
                            <div class="overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Time') }}</th>
                                            <th>{{ __('Room') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${meetings.map(meeting => `
                                            <tr>
                                                <td>${meeting.date}</td>
                                                <td>${meeting.time}</td>
                                                <td><span class="badge badge-accent">${meeting.room || '{{ __('Not specified') }}'}</span></td>
                                                <td><span class="badge badge-${meeting.status_color}">${meeting.status_label}</span></td>
                                                <td>
                                                    <a href="/admin/meetings/${meeting.id}" class="btn btn-xs btn-primary">
                                                        {{ __('View') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                    document.getElementById('user-meetings-section').classList.remove('hidden');
                }

                // Update recent scans
                function updateRecentScans() {
                    if (scans.length === 0) {
                        recentScans.innerHTML = `<p class="text-base-content/70 text-sm">{{ __('No recent scans') }}</p>`;
                        return;
                    }

                    recentScans.innerHTML = '';
                    scans.slice(0, 5).forEach(scan => {
                        const scanEl = document.createElement('div');
                        scanEl.className = 'py-2 border-b border-base-200 last:border-0';
                        scanEl.innerHTML = `
                            <p class="text-sm font-medium">${scan.name}</p>
                            <p class="text-xs text-base-content/70">${scan.email}</p>
                            ${scan.organization ? `<p class="text-xs text-base-content/70">${scan.organization}</p>` : ''}
                            <p class="text-xs text-base-content/70">${scan.time}</p>
                        `;
                        recentScans.appendChild(scanEl);
                    });
                }

                // Stop scanner when user leaves the page
                window.addEventListener('beforeunload', function() {
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
