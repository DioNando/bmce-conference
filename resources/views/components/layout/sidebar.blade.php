    <!-- Static sidebar for desktop only -->
<div id="sidebar" class="hidden lg:flex lg:fixed h-full">
    <!-- Sidebar component, swap this element with another sidebar if you like -->
    <div class="sidebar-content flex grow flex-col gap-y-5 overflow-y-auto overflow-x-hidden scrollbar-custom bg-primary py-6">
        <div class="flex h-16 shrink-0 items-center justify-center">
            <a href="/dashboard" x-bind:class="sidebarCollapsed ? 'mx-auto' : ''">
                <x-application-logo x-bind:class="sidebarCollapsed ? 'w-10' : ''" />
            </a>
        </div>
        <hr class="border-gray-200" x-show="!sidebarCollapsed" x-cloak />
        <nav class="flex flex-1 flex-col" x-bind:class="sidebarCollapsed ? ' items-center' : ''" x-cloak>
            <x-layout.navigation-links />
        </nav>
    </div>
</div>
