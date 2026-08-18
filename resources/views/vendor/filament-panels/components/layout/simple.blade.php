@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }

    $isGuestSimplePage = ! filament()->auth()->check();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="fi-simple-layout">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}

        @if (($hasTopbar ?? true) && filament()->auth()->check())
            <div class="fi-simple-layout-header">
                @if (filament()->hasDatabaseNotifications())
                    @livewire(filament()->getDatabaseNotificationsLivewireComponent(), [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                        'position' => \Filament\Enums\DatabaseNotificationsPosition::Topbar,
                    ])
                @endif

                @if (filament()->hasUserMenu())
                    @livewire(Filament\Livewire\SimpleUserMenu::class)
                @endif
            </div>
        @endif

        @if ($isGuestSimplePage)
            <style>
                .pos-auth-shell {
                    min-height: 100vh;
                    display: grid;
                    grid-template-columns: minmax(22rem, 0.95fr) minmax(24rem, 1.05fr);
                    background: linear-gradient(135deg, rgba(15, 23, 42, 0.98), #030712 48%, rgba(4, 47, 46, 0.96));
                    color: #f8fafc;
                }

                .pos-auth-brand {
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    gap: 2rem;
                    padding: clamp(2rem, 5vw, 5.5rem);
                    border-right: 1px solid rgba(148, 163, 184, 0.16);
                    background-image:
                        linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                        linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
                    background-size: 38px 38px;
                }

                .pos-auth-brand-mark {
                    width: 4.5rem;
                    height: 4.5rem;
                    border-radius: 1.25rem;
                    display: grid;
                    place-items: center;
                    background: rgba(16, 185, 129, 0.16);
                    border: 1px solid rgba(52, 211, 153, 0.34);
                    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.22);
                    overflow: hidden;
                }

                .pos-auth-brand-mark img {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    padding: 0.7rem;
                }

                .pos-auth-brand-mark span {
                    font-size: 2rem;
                    font-weight: 900;
                    color: #34d399;
                }

                .pos-auth-kicker {
                    margin: 0 0 0.85rem;
                    font-size: 0.78rem;
                    font-weight: 800;
                    letter-spacing: 0.12em;
                    text-transform: uppercase;
                    color: #5eead4;
                }

                .pos-auth-brand h1 {
                    margin: 0;
                    max-width: 12ch;
                    font-size: clamp(2.4rem, 5vw, 4.7rem);
                    line-height: 0.98;
                    font-weight: 900;
                    letter-spacing: 0;
                }

                .pos-auth-copy {
                    max-width: 34rem;
                    margin: 1.25rem 0 0;
                    font-size: 1rem;
                    line-height: 1.8;
                    color: #cbd5e1;
                }

                .pos-auth-status-grid {
                    display: grid;
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                    gap: 0.85rem;
                    max-width: 34rem;
                }

                .pos-auth-status-grid div {
                    border-radius: 0.75rem;
                    border: 1px solid rgba(148, 163, 184, 0.18);
                    background: rgba(2, 6, 23, 0.42);
                    padding: 0.9rem;
                }

                .pos-auth-status-grid strong,
                .pos-auth-status-grid span {
                    display: block;
                }

                .pos-auth-status-grid strong {
                    font-size: 0.85rem;
                    color: #f8fafc;
                }

                .pos-auth-status-grid span {
                    margin-top: 0.2rem;
                    font-size: 0.74rem;
                    font-weight: 700;
                    color: #94a3b8;
                }

                .pos-auth-form-wrap {
                    min-height: 100vh;
                    display: grid;
                    place-items: center;
                    padding: clamp(1.25rem, 4vw, 4rem);
                    background: #050507;
                }

                .pos-auth-form {
                    width: min(100%, 28rem) !important;
                    max-width: 28rem !important;
                    border-radius: 1.25rem !important;
                    border: 1px solid rgba(148, 163, 184, 0.16) !important;
                    background: rgba(17, 17, 19, 0.94) !important;
                    box-shadow: 0 28px 80px rgba(0, 0, 0, 0.36) !important;
                    padding: clamp(1.5rem, 4vw, 2.25rem) !important;
                }

                .pos-auth-form .fi-logo,
                .pos-auth-form .fi-simple-header-logo {
                    justify-content: flex-start !important;
                }

                .pos-auth-form .fi-simple-header-heading {
                    text-align: left !important;
                    font-size: 1.55rem !important;
                    line-height: 1.2 !important;
                    font-weight: 900 !important;
                    color: #ffffff !important;
                }

                .pos-auth-form .fi-simple-header-subheading {
                    text-align: left !important;
                    color: #94a3b8 !important;
                }

                .pos-auth-form .fi-input-wrp {
                    min-height: 3.15rem !important;
                    border-radius: 0.8rem !important;
                    background: #050507 !important;
                    border-color: rgba(148, 163, 184, 0.2) !important;
                }

                .pos-auth-form .fi-input {
                    min-height: 3.15rem !important;
                    font-size: 0.95rem !important;
                    color: #f8fafc !important;
                }

                .pos-auth-form .fi-btn {
                    min-height: 3.1rem !important;
                    border-radius: 0.8rem !important;
                    font-weight: 900 !important;
                }

                .pos-auth-form .fi-btn-color-primary {
                    background: #10b981 !important;
                    color: #02110c !important;
                    box-shadow: 0 16px 34px rgba(16, 185, 129, 0.22) !important;
                }

                .pos-auth-form .fi-btn-color-primary:hover {
                    background: #34d399 !important;
                }

                @media (max-width: 900px) {
                    .pos-auth-shell {
                        grid-template-columns: 1fr;
                    }

                    .pos-auth-brand {
                        min-height: auto;
                        padding: 2rem 1.25rem 1rem;
                        border-right: 0;
                        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
                    }

                    .pos-auth-brand h1 {
                        max-width: none;
                        font-size: 2.25rem;
                    }

                    .pos-auth-copy {
                        display: none;
                    }

                    .pos-auth-form-wrap {
                        min-height: auto;
                        place-items: start center;
                        padding: 1.25rem;
                    }
                }

                @media (max-width: 520px) {
                    .pos-auth-status-grid {
                        display: none;
                    }
                }
            </style>

            <div class="pos-auth-shell">
                <section class="pos-auth-brand" aria-label="Store sign in">
                    <div class="pos-auth-brand-mark">
                        @if (filament()->getBrandLogo())
                            <img src="{{ filament()->getBrandLogo() }}" alt="{{ filament()->getBrandName() }}" />
                        @else
                            <span>{{ strtoupper(substr(filament()->getBrandName() ?? 'POS', 0, 1)) }}</span>
                        @endif
                    </div>

                    <div>
                        <p class="pos-auth-kicker">Retail control center</p>
                        <h1>{{ filament()->getBrandName() }}</h1>
                        <p class="pos-auth-copy">Sign in to manage checkout, inventory, customers, lends, and daily store activity from one secure workspace.</p>
                    </div>

                    <div class="pos-auth-status-grid">
                        <div>
                            <strong>POS</strong>
                            <span>Ready</span>
                        </div>
                        <div>
                            <strong>Stock</strong>
                            <span>Tracked</span>
                        </div>
                        <div>
                            <strong>Sales</strong>
                            <span>Synced</span>
                        </div>
                    </div>
                </section>

                <div class="pos-auth-form-wrap">
                    <main class="fi-simple-main pos-auth-form">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        @else
            <div class="fi-simple-main-ctn">
                <main
                    @class([
                        'fi-simple-main',
                        ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                    ])
                >
                    {{ $slot }}
                </main>
            </div>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>
