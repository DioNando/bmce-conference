<ul role="list" class="flex flex-1 flex-col gap-y-6">
    <li>
        <ul role="list" class="-mx-2 space-y-2 sidebar-nav">
            @if (auth()->user()->hasRole(\App\Enums\UserRole::ADMIN->value))
                {{-- ADMIN --}}
                <li>
                    <x-layout.link-primary route="admin.dashboard" icon="squares-2x2" label="Dashboard" />
                </li>
                <li>
                    <x-layout.link-primary route="admin.users.index" icon="user-group" label="Account Management" />
                </li>

                <li>
                    <x-layout.link-primary route="admin.meetings.index" icon="calendar" label="Meetings Management" />
                </li>
                <li>
                    <x-layout.link-primary route="admin.organizations.index" icon="building-office"
                        label="Organizations List" />
                </li>
                <li>
                    <x-layout.link-primary route="admin.rooms.index" icon="building-library" label="Room Management" />
                </li>
                <li>
                    <x-layout.link-primary route="admin.administrators" icon="users"
                        label="Administrator Management" />
                </li>
                {{-- ADMIN --}}
            @endif

            @if (auth()->user()->hasRole(\App\Enums\UserRole::INVESTOR->value))
                {{-- INVESTOR --}}
                <li>
                    <x-layout.link-primary route="investor.dashboard" icon="squares-2x2" label="Home" />
                </li>
                <li>
                    <x-layout.link-primary route="investor.qr-code.show" icon="qr-code" label="QR Code" />
                </li>
                <li>
                    <x-layout.link-primary route="investor.meetings.index" icon="clipboard-document-list"
                        label="Meetings" />
                </li>
                <li>
                    <x-layout.link-primary route="investor.issuers.index" icon="building-office-2"
                        label="Issuers Directory" />
                </li>
                {{-- INVESTOR --}}
            @endif

            @if (auth()->user()->hasRole(\App\Enums\UserRole::ISSUER->value))
                {{-- ISSUER --}}
                <li>
                    <x-layout.link-primary route="issuer.dashboard" icon="squares-2x2" label="Home" />
                </li>
                <li>
                    <x-layout.link-primary route="issuer.qr-code.show" icon="qr-code" label="QR Code" />
                </li>
                <li>
                    <x-layout.link-primary route="issuer.meetings.index" icon="calendar" label="Meetings" />
                </li>
                <li>
                    <x-layout.link-primary route="issuer.schedule" icon="clock" label="Schedule" />
                </li>
                {{-- ISSUER --}}
            @endif
        </ul>
    </li>
    <li class="mt-auto">
        <ul role="list" class="-mx-2 space-y-2 sidebar-nav">
            @if (auth()->user()->hasRole(\App\Enums\UserRole::ADMIN->value))
                <li>
                    <x-layout.link-primary route="admin.diagrams.index" icon="square-3-stack-3d" label="Diagrammes" />
                </li>
            @endif
            <li>
                <x-layout.link-primary route="profile.edit" icon="user" label="Profile" />
            </li>
            <!-- Authentication -->
            {{-- <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="sidebar-link cursor-pointer w-full group flex items-center gap-x-2 rounded-lg px-2 py-1.5 text-sm/6 font-semibold
                        text-gray-100 hover:text-primary hover:bg-white outline-2 -outline-offset-1 outline-gray-300">
                        <x-heroicon-s-arrow-right-on-rectangle class="sidebar-icon shrink-0 text-gray-100 group-hover:text-primary" />
                        <span class="sidebar-label">{{ __('Log Out') }}</span>
                    </button>
                </form>
            </li> --}}
        </ul>
    </li>
</ul>
