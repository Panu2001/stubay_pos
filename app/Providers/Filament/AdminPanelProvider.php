<?php

namespace App\Providers\Filament;

use App\Filament\GlobalSearch\NavigationSearchProvider;
use App\Filament\Pages\Dashboard;
use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            // ->globalSearch(NavigationSearchProvider::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName(fn () => Setting::get('store_name', 'EASY POS'))
            ->brandLogo(fn () => Setting::get('store_logo') ? asset('storage/'.Setting::get('store_logo')) : null)
            ->brandLogoHeight('3rem')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Inventory')
                    ->icon('heroicon-o-archive-box'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Store Management')
                    ->icon('heroicon-o-building-storefront'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->defaultThemeMode(\Filament\Enums\ThemeMode::Dark)
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Zinc,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('
                    <script src="https://cdn.jsdelivr.net/npm/lucide@0.263.0/dist/umd/lucide.min.js"></script>
                    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        .fi-theme-switcher { display: none !important; }
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
                    </script>
                    ')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('
                    <div x-data x-on:print-receipt.window="$refs.receiptFrame.src = $event.detail.url" x-on:print-cash-receipt.window="$refs.receiptFrame.src = $event.detail.url">
                        <iframe x-ref="receiptFrame" style="display:none;" src="about:blank"></iframe>
                    </div>
                    <script>
                        window.posRefreshLucideIcons = function (delay = 0) {
                            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, delay);
                        };
                        window.posRefreshLucideIcons(100);
                        document.addEventListener("livewire:navigated", () => window.posRefreshLucideIcons(30));
                        document.addEventListener("livewire:update", () => window.posRefreshLucideIcons(30));
                        document.addEventListener("livewire:updated", () => window.posRefreshLucideIcons(30));
                        document.addEventListener("livewire:morph.updated", () => window.posRefreshLucideIcons(30));
                    </script>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
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

