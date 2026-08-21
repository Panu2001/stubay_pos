@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $hasTopbar = filament()->hasTopbar();
    $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
    $isSidebarFullyCollapsibleOnDesktop = filament()->isSidebarFullyCollapsibleOnDesktop();
    $hasTopNavigation = filament()->hasTopNavigation();
    $hasNavigation = filament()->hasNavigation();
    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getMaxContentWidth() ?? Width::SevenExtraLarge);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }
@endphp

<x-filament-panels::layout.base
    :livewire="$livewire"
>
    <style>
        .pos-header-user-chip {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.25rem 0.75rem 0.25rem 0.35rem;
            background-color: #111827;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .pos-header-avatar {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 0.375rem;
            background: linear-gradient(135deg, #059669, #0d9488);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .pos-header-user-meta {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .pos-header-user-name {
            font-size: 0.75rem;
            font-weight: 700;
            color: #f3f4f6;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
        }

        .pos-header-user-role {
            font-size: 0.65rem;
            font-weight: 600;
            color: #34d399;
            line-height: 1;
            margin-top: 0.15rem;
        }

        .pos-header-btn {
            width: 2.25rem;
            height: 2.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            background-color: #111827;
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #9ca3af;
            cursor: pointer;
            transition: all 150ms ease;
            position: relative;
        }

        .pos-header-btn:hover {
            color: #ffffff;
            background-color: #1f2937;
            border-color: rgba(255, 255, 255, 0.15);
        }

        .pos-header-btn:active {
            transform: scale(0.95);
        }

        .pos-header-btn-logout:hover {
            color: #fb7185;
            background-color: rgba(244, 63, 94, 0.12);
            border-color: rgba(244, 63, 94, 0.25);
        }

        .pos-header-divider {
            width: 1px;
            height: 1.25rem;
            background-color: rgba(255, 255, 255, 0.1);
            margin: auto 0;
        }

        .pos-side-panel-scroll {
            flex: 1 1 0% !important;
            min-height: 0 !important;
            max-height: 100% !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.4) transparent;
        }

        .pos-side-panel-scroll > * {
            flex-shrink: 0 !important;
        }

        .pos-side-panel-scroll::-webkit-scrollbar {
            width: 0.4rem;
        }

        .pos-side-panel-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .pos-side-panel-scroll::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 999px;
        }

        .pos-side-panel-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.6);
        }

        .pos-dashboard-shell {
            height: 100vh;
            overflow: hidden;
        }

        .pos-dashboard-main {
            min-height: 0;
        }

        .pos-dashboard-main > main {
            min-height: 0 !important;
            overflow: auto !important;
        }

        /* Fluid Desktop Sidebar Sliding Transition */
        @media (min-width: 768px) {
            .pos-desktop-sidebar {
                display: flex !important;
            }
        }
        @media (max-width: 767px) {
            .pos-desktop-sidebar {
                display: none !important;
            }
        }

        .pos-desktop-sidebar {
            height: 100vh !important;
            max-height: 100vh !important;
            width: 16rem !important;
            min-width: 16rem !important;
            max-width: 16rem !important;
            transition: width 300ms cubic-bezier(0.4, 0, 0.2, 1), min-width 300ms cubic-bezier(0.4, 0, 0.2, 1), max-width 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
            overflow: hidden !important;
            will-change: width, min-width, max-width;
        }

        .pos-desktop-sidebar.pos-sidebar-collapsed {
            width: 4.5rem !important;
            min-width: 4.5rem !important;
            max-width: 4.5rem !important;
        }

        .pos-sidebar-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0 0.25rem;
            margin-bottom: 1.75rem;
            min-height: 2.25rem;
            overflow: hidden;
            position: relative;
            flex-shrink: 0;
        }

        .pos-sidebar-logo {
            width: 2rem;
            height: 2rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #10b981;
            border-radius: 0;
            transition: opacity 220ms ease, transform 260ms cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .pos-sidebar-collapsed .pos-sidebar-logo {
            opacity: 0 !important;
            transform: translateX(-12px) !important;
            pointer-events: none !important;
        }

        .pos-sidebar-toggle-btn {
            margin-left: auto;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            outline: none;
            border-radius: 0;
            color: #9ca3af;
            cursor: pointer;
            flex-shrink: 0;
            transition: color 200ms ease, transform 300ms cubic-bezier(0.4, 0, 0.2, 1), left 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .pos-sidebar-toggle-btn:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .pos-sidebar-collapsed .pos-sidebar-toggle-btn {
            position: absolute !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            margin: 0 !important;
        }

        .pos-sidebar-collapsed .sidebar-btn {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            border-left: none !important;
        }

        .pos-sidebar-label {
            display: inline-block;
            max-width: 180px;
            opacity: 1;
            transform: translateX(0);
            transition: opacity 220ms ease, transform 260ms cubic-bezier(0.4, 0, 0.2, 1), max-width 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
            overflow: hidden !important;
            text-overflow: clip !important;
            white-space: nowrap !important;
        }

        .pos-sidebar-collapsed .pos-sidebar-label {
            max-width: 0 !important;
            opacity: 0 !important;
            transform: translateX(-12px) !important;
            pointer-events: none !important;
        }

        .pos-sidebar-group-title {
            max-height: 2rem;
            opacity: 1;
            transform: translateX(0);
            transition: opacity 200ms ease, transform 260ms cubic-bezier(0.4, 0, 0.2, 1), max-height 300ms cubic-bezier(0.4, 0, 0.2, 1), margin 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
            overflow: hidden !important;
            white-space: nowrap !important;
        }

        .pos-sidebar-collapsed .pos-sidebar-group-title {
            max-height: 0 !important;
            opacity: 0 !important;
            margin-bottom: 0 !important;
            transform: translateX(-12px) !important;
            pointer-events: none !important;
        }

        .pos-sidebar-badge {
            opacity: 1;
            transform: scale(1);
            transition: opacity 180ms ease, transform 260ms cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .pos-sidebar-collapsed .pos-sidebar-badge {
            opacity: 0 !important;
            transform: scale(0.6) !important;
            pointer-events: none !important;
        }

        .pos-sidebar-chevron {
            transition: transform 300ms cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .pos-sidebar-collapsed .pos-sidebar-chevron {
            transform: rotate(180deg) !important;
        }
    </style>

    <div
        x-data="{
            sidebarExpanded: JSON.parse(localStorage.getItem('posSidebarExpanded') ?? 'true'),
            mobileSidebarOpen: false,
            toggleSidebar() {
                this.sidebarExpanded = ! this.sidebarExpanded;
                localStorage.setItem('posSidebarExpanded', JSON.stringify(this.sidebarExpanded));
            },
            toggleMobileSidebar() {
                this.mobileSidebarOpen = ! this.mobileSidebarOpen;
            },
            closeMobileSidebar() {
                this.mobileSidebarOpen = false;
            }
        }"
        class="pos-dashboard-shell w-full flex relative overflow-hidden"
    >
        <!-- Mobile / Tablet Backdrop Overlay -->
        <div
            x-show="mobileSidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="closeMobileSidebar()"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 lg:hidden"
            style="display: none;"
        ></div>

        <!-- Mobile & Tablet Slide-out Drawer Panel -->
        <aside
            x-show="mobileSidebarOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-gray-900 border-r border-gray-800 flex flex-col p-4 shadow-2xl lg:hidden"
            style="display: none;"
        >
            <div class="mb-6 flex items-center justify-between px-2 shrink-0 border-b border-gray-800/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-none bg-emerald-500 flex items-center justify-center shrink-0 shadow-none">
                        <x-filament::icon
                            icon="heroicon-o-shopping-bag"
                            class="w-5 h-5 text-white"
                        />
                    </div>
                    <span class="font-bold text-white text-base truncate">
                        {{ \App\Models\Setting::get('store_name', 'EASY POS') }}
                    </span>
                </div>

                <button
                    type="button"
                    @click="closeMobileSidebar()"
                    class="flex h-8 w-8 items-center justify-center rounded-none bg-transparent hover:bg-transparent text-gray-400 hover:text-white border-0 shadow-none outline-none focus:outline-none focus:ring-0 transition-colors"
                    title="Close navigation menu"
                >
                    <x-filament::icon
                        icon="heroicon-o-x-mark"
                        class="w-5 h-5"
                    />
                </button>
            </div>

            <nav class="pos-side-panel-scroll flex flex-col gap-4 flex-1 pr-1">
                @foreach (filament()->getNavigation() as $group)
                    <div class="px-1 shrink-0">
                        @if (filled($group->getLabel()))
                            <p class="text-[11px] uppercase tracking-wider text-gray-500 font-bold mb-2 px-2 truncate">
                                {{ $group->getLabel() }}
                            </p>
                        @endif
                        <div class="space-y-1">
                            @foreach ($group->getItems() as $item)
                                @php
                                    $isActive = $item->isActive();
                                @endphp
                                <a
                                    href="{{ $item->getUrl() }}"
                                    @if($item->shouldOpenUrlInNewTab()) target="_blank" @endif
                                    @click="closeMobileSidebar()"
                                    class="sidebar-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-none {{ $isActive ? 'text-emerald-400 bg-emerald-500/10 font-bold border-l-2 border-emerald-500' : 'text-gray-400 hover:text-white hover:bg-gray-800 font-medium' }} text-sm transition-colors"
                                >
                                    @if ($icon = $item->getIcon())
                                        <x-filament::icon
                                            :icon="$icon"
                                            class="w-5 h-5 flex-shrink-0 {{ $isActive ? 'text-emerald-400' : 'text-gray-400' }}"
                                        />
                                    @endif
                                    <span class="truncate">
                                        {{ $item->getLabel() }}
                                    </span>
                                    @if (filled($badge = $item->getBadge()))
                                        <span
                                            class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-none text-xs font-semibold {{ $isActive ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-800 text-gray-300' }}"
                                        >
                                            {{ $badge }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="pb-8"></div>
            </nav>
        </aside>

        <!-- Desktop Persistent Sidebar with Smooth Sliding Animation -->
        <aside
            :class="sidebarExpanded ? 'pos-sidebar-expanded' : 'pos-sidebar-collapsed'"
            class="pos-desktop-sidebar h-screen sticky top-0 bg-gray-900 border-r border-gray-800 flex-col p-3 shrink-0"
        >
            <div class="pos-sidebar-header">
                <div class="pos-sidebar-logo">
                    <x-filament::icon
                        icon="heroicon-o-shopping-bag"
                        class="w-4 h-4 text-white"
                    />
                </div>
                <span class="pos-sidebar-label font-bold text-white text-sm tracking-tight flex-1">
                    {{ \App\Models\Setting::get('store_name', 'EASY POS') }}
                </span>

                <button
                    type="button"
                    x-on:click="toggleSidebar()"
                    class="pos-sidebar-toggle-btn"
                    x-bind:title="sidebarExpanded ? 'Collapse side panel' : 'Expand side panel'"
                >
                    <x-filament::icon
                        icon="heroicon-o-chevron-double-left"
                        class="pos-sidebar-chevron w-4 h-4"
                    />
                </button>
            </div>
            
            <nav
                class="pos-side-panel-scroll flex flex-col gap-4 flex-1 pr-1"
            >
                @foreach (filament()->getNavigation() as $group)
                    <div class="px-1 shrink-0 overflow-hidden">
                        @if (filled($group->getLabel()))
                            <div class="pos-sidebar-group-title">
                                <p class="text-[11px] uppercase tracking-wider text-gray-500 font-bold mb-2 truncate">
                                    {{ $group->getLabel() }}
                                </p>
                            </div>
                        @endif
                        <div class="space-y-1">
                            @foreach ($group->getItems() as $item)
                                @php
                                    $isActive = $item->isActive();
                                @endphp
                                <a
                                    href="{{ $item->getUrl() }}"
                                    @if($item->shouldOpenUrlInNewTab()) target="_blank" @endif
                                    class="sidebar-btn w-full flex items-center gap-3 px-2.5 py-2.5 rounded-none {{ $isActive ? 'text-emerald-400 bg-emerald-500/10 font-bold border-l-2 border-emerald-500' : 'text-gray-400 hover:text-white hover:bg-gray-800/80 font-medium' }} text-sm transition-colors overflow-hidden"
                                    x-bind:title="sidebarExpanded ? null : @js($item->getLabel())"
                                >
                                    @if ($icon = $item->getIcon())
                                        <x-filament::icon
                                            :icon="$icon"
                                            class="w-5 h-5 flex-shrink-0 {{ $isActive ? 'text-emerald-400' : 'text-gray-400' }}"
                                        />
                                    @endif
                                    <span class="pos-sidebar-label flex-1 truncate">
                                        {{ $item->getLabel() }}
                                    </span>
                                    @if (filled($badge = $item->getBadge()))
                                        <span
                                            class="pos-sidebar-badge ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-none text-xs font-semibold {{ $isActive ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-800 text-gray-300' }}"
                                        >
                                            {{ $badge }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                <!-- Spacer -->
                <div class="pb-10"></div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="pos-dashboard-main flex-1 flex flex-col h-screen overflow-hidden min-w-0">
            <!-- Top Bar -->
            <header class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-800 bg-gray-900/50 shrink-0">
                <div class="flex items-center gap-3">
                    <!-- Options button on top-left corner for mobile screens only -->
                    <button
                        type="button"
                        @click="toggleMobileSidebar()"
                        class="md:hidden flex items-center justify-center p-1.5 rounded-none bg-transparent hover:bg-transparent text-gray-300 hover:text-white border-0 shadow-none outline-none focus:outline-none focus:ring-0 transition-colors"
                        title="Open navigation menu"
                    >
                        <x-filament::icon
                            icon="heroicon-o-bars-3"
                            class="w-6 h-6 text-gray-300 hover:text-white"
                        />
                    </button>

                    <div>
                        <h1 class="font-bold text-white text-lg sm:text-xl leading-tight">{{ $livewire?->getTitle() ?? 'Dashboard' }}</h1>
                        <p class="text-gray-500 text-xs mt-0.5">{{ now()->format('l, F j, Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-2.5">
                    <!-- Notifications Button -->
                    <button 
                        type="button"
                        class="pos-header-btn"
                        title="Notifications"
                    >
                        <x-filament::icon
                            icon="heroicon-o-bell"
                            class="w-4 h-4 text-gray-300"
                        />
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-emerald-500 rounded-full ring-2 ring-gray-900"></span>
                    </button>

                    <!-- User Profile Chip -->
                    <div class="pos-header-user-chip">
                        <div class="pos-header-avatar">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="pos-header-user-meta">
                            <span class="pos-header-user-name">
                                {{ auth()->user()->name ?? 'User' }}
                            </span>
                            <span class="pos-header-user-role">
                                {{ ucwords(str_replace('_', ' ', auth()->user()->role ?? 'Admin')) }}
                            </span>
                        </div>
                    </div>

                    <!-- Subtle Divider -->
                    <div class="pos-header-divider"></div>

                    <!-- Logout Button -->
                    <form action="{{ filament()->getLogoutUrl() }}" method="post" class="inline-flex m-0 p-0">
                        @csrf
                        <button 
                            type="submit" 
                            class="pos-header-btn pos-header-btn-logout" 
                            title="Sign out"
                        >
                            <x-filament::icon
                                icon="heroicon-o-arrow-left-on-rectangle"
                                class="w-4 h-4"
                            />
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 min-h-0 overflow-auto bg-gray-950 p-4 lg:p-6 text-white">
                <div class="w-full">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-filament-panels::layout.base>
