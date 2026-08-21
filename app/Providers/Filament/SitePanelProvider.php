<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SitePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('site')
            ->path('site')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->defaultThemeMode(\Filament\Enums\ThemeMode::Dark)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName(fn () => \App\Models\Setting::get('store_name', 'EASY POS'))
            ->brandLogo(fn () => \App\Models\Setting::get('store_logo') ? asset('storage/' . \App\Models\Setting::get('store_logo')) : null)
            ->brandLogoHeight('3rem')
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render('<style>
                    .fi-theme-switcher { display: none !important; }
                    body {
                        background: radial-gradient(circle at top left, #1f1c2c 0%, #928DAB 100%) !important;
                    }
                    .fi-sidebar, .fi-topbar {
                        background: rgba(24, 24, 27, 0.5) !important;
                        backdrop-filter: blur(12px) !important;
                        -webkit-backdrop-filter: blur(12px) !important;
                        border-color: rgba(255, 255, 255, 0.05) !important;
                    }
                    .fi-pc, .fi-ta-ctn {
                        background: rgba(24, 24, 27, 0.6) !important;
                        backdrop-filter: blur(12px) !important;
                        -webkit-backdrop-filter: blur(12px) !important;
                        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3) !important;
                    }
                    button[title="Open navigation menu"],
                    button[title="Close navigation menu"],
                    button[title*="Collapse side panel"],
                    button[title*="Expand side panel"],
                    .fi-topbar-open-sidebar-btn,
                    .fi-topbar-close-sidebar-btn,
                    .fi-topbar-open-collapse-sidebar-btn,
                    .fi-topbar-close-collapse-sidebar-btn,
                    .fi-layout-sidebar-toggle-btn,
                    .fi-sidebar-open-collapse-sidebar-btn,
                    .fi-sidebar-close-collapse-sidebar-btn,
                    .fi-topbar-collapse-sidebar-btn-ctn button {
                        border-radius: 0 !important;
                        border: none !important;
                        box-shadow: none !important;
                        outline: none !important;
                        background-color: transparent !important;
                        background: transparent !important;
                        --tw-ring-shadow: none !important;
                        --tw-shadow: none !important;
                    }
                    .fi-logo,
                    .fi-logo img,
                    .fi-logo-light,
                    .fi-logo-dark,
                    .fi-sidebar-header-logo-ctn,
                    .fi-sidebar-header-logo-ctn img,
                    .fi-sidebar-header-logo-ctn > div,
                    .fi-topbar-start img,
                    .fi-topbar-start > div,
                    .pos-auth-logo,
                    .pos-auth-logo img {
                        border-radius: 0 !important;
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
                <script>
                    (function () {
                        localStorage.setItem("theme", "dark");
                        document.documentElement.classList.add("dark");
                        const observer = new MutationObserver(function () {
                            if (!document.documentElement.classList.contains("dark")) {
                                document.documentElement.classList.add("dark");
                            }
                        });
                        observer.observe(document.documentElement, { attributes: true, attributeFilter: ["class"] });
                    })();
                </script>')
            )
            ->discoverResources(in: app_path('Filament/Site/Resources'), for: 'App\Filament\Site\Resources')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Site/Pages'), for: 'App\Filament\Site\Pages')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Site/Widgets'), for: 'App\Filament\Site\Widgets')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
