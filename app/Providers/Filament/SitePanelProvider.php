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
