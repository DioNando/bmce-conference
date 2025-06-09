<nav x-data="{ open: false }"
    class="bg-primary lg:bg-base-100 border-b border-gray-300 shadow-md sticky text-white lg:text-gray-900 top-0 w-full z-50">
    <!-- Primary Navigation Menu -->
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="items-center block lg:hidden">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-base-content" />
                    </a>
                </div>


                <!-- Sidebar Toggle -->
                <div class="hidden lg:flex items-center mr-4">
                    <button @click="sidebarCollapsed = !sidebarCollapsed"
                        class="btn btn-circle btn-sm btn-soft btn-primary">
                        <x-heroicon-s-chevron-left x-bind:class="sidebarCollapsed ? 'rotate-180' : ''" x-cloak
                            class="size-4" />
                    </button>
                </div>
                {{-- <div class="max-w-lg">
                    <label class="input">
                        <x-heroicon-s-magnifying-glass class="size-4 opacity-50" />
                        <input type="search" class="grow" name="search" placeholder="Search users..."
                            value="{{ request('search') }}" />
                    </label>
                </div> --}}
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @livewire('notifications')
                <!-- Language Switcher -->
                <div class="mr-4">
                    <a href="{{ route('language.switch', 'fr') }}"
                        class="join-item btn btn-sm font-mono rounded-full {{ app()->getLocale() == 'fr' ? 'btn-primary' : 'btn-ghost' }}">FR</a>
                    <a href="{{ route('language.switch', 'en') }}"
                        class="join-item btn btn-sm font-mono rounded-full {{ app()->getLocale() == 'en' ? 'btn-primary' : 'btn-ghost' }}">EN</a>
                </div>

                <div class="mr-2">
                    @if (Auth::user()->profile_photo_path)
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}">
                    @elseif (Auth::user()->hasRole(\App\Enums\UserRole::ADMIN->value))
                        <div
                            class="h-10 w-10 rounded-full bg-neutral/10 flex items-center justify-center text-neutral font-mono font-semibold">
                            {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @elseif (auth()->user()->hasRole(\App\Enums\UserRole::ISSUER->value))
                        <div
                            class="h-10 w-10 rounded-full bg-warning/10 flex items-center justify-center text-warning font-mono font-semibold">
                            {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @elseif (auth()->user()->hasRole(\App\Enums\UserRole::INVESTOR->value))
                        <div
                            class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-mono font-semibold">
                            {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                        <div>{{ Auth::user()->first_name . ' ' . Auth::user()->name }}</div>
                        {{-- <x-heroicon-s-chevron-down class="size-3" /> --}}
                        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                        </svg>
                    </div>

                    <ul tabindex="0"
                        class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-box w-48 z-[1] text-gray-900">
                        <li>
                            <a href="{{ route('profile.edit') }}">
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <div class="mr-2">
                    @if (Auth::user()->profile_photo_path)
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}">
                    @elseif (Auth::user()->hasRole(\App\Enums\UserRole::ADMIN->value))
                        <div
                            class="h-10 w-10 rounded-full bg-neutral flex items-center justify-center text-white font-mono font-semibold">
                            {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @elseif (auth()->user()->hasRole(\App\Enums\UserRole::ISSUER->value))
                        <div
                            class="h-10 w-10 rounded-full bg-warning flex items-center justify-center text-white font-mono font-semibold">
                            {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @elseif (auth()->user()->hasRole(\App\Enums\UserRole::INVESTOR->value))
                        <div
                            class="h-10 w-10 rounded-full bg-primary flex items-center justify-center text-white font-mono font-semibold">
                            {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="dropdown dropdown-end">
                    <button @click="open = ! open" class="btn btn-primary btn-circle btn-sm" tabindex="0">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div tabindex="0"
                        class="dropdown-content z-[99] menu p-2 shadow-xl bg-base-100 rounded-box w-72 mt-6 mobile-menu-dropdown">
                        <div class="px-4 py-2 border-b border-base-200 mb-2">
                            <div class="font-medium text-base text-base-content">
                                {{ Auth::user()->name . ' ' . Auth::user()->first_name }}</div>
                            <div class="font-medium text-sm text-base-content/70">{{ Auth::user()->email }}</div>

                            <!-- Language Switcher (Mobile) -->
                            <div class="mt-2 text-gray-900">
                                <a href="{{ route('language.switch', 'fr') }}"
                                    class="btn btn-sm font-mono rounded-full {{ app()->getLocale() == 'fr' ? 'btn-primary' : 'btn-ghost' }}">FR</a>
                                <a href="{{ route('language.switch', 'en') }}"
                                    class="btn btn-sm font-mono rounded-full {{ app()->getLocale() == 'en' ? 'btn-primary' : 'btn-ghost' }}">EN</a>
                            </div>
                        </div>
                        <div class="px-2 pb-2">
                            <x-layout.navigation-links />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
