<?php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }

    $isGuestSimplePage = ! filament()->auth()->check();
?>

<?php if (isset($component)) { $__componentOriginale960ae7ad1b1ce9e3596e483505fadc9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale960ae7ad1b1ce9e3596e483505fadc9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.layout.base','data' => ['livewire' => $livewire]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::layout.base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['livewire' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($livewire)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

    <div class="fi-simple-layout">
        <?php echo e(\Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes)); ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($hasTopbar ?? true) && filament()->auth()->check()): ?>
            <div class="fi-simple-layout-header">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filament()->hasDatabaseNotifications()): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split(filament()->getDatabaseNotificationsLivewireComponent(), [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                        'position' => \Filament\Enums\DatabaseNotificationsPosition::Topbar,
                    ]);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1304879379-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filament()->hasUserMenu()): ?>
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split(Filament\Livewire\SimpleUserMenu::class);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1304879379-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isGuestSimplePage): ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filament()->getBrandLogo()): ?>
                            <img src="<?php echo e(filament()->getBrandLogo()); ?>" alt="<?php echo e(filament()->getBrandName()); ?>" />
                        <?php else: ?>
                            <span><?php echo e(strtoupper(substr(filament()->getBrandName() ?? 'POS', 0, 1))); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div>
                        <p class="pos-auth-kicker">Retail control center</p>
                        <h1><?php echo e(filament()->getBrandName()); ?></h1>
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
                        <?php echo e($slot); ?>

                    </main>
                </div>
            </div>
        <?php else: ?>
            <div class="fi-simple-main-ctn">
                <main
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'fi-simple-main',
                        ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                    ]); ?>"
                >
                    <?php echo e($slot); ?>

                </main>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo e(\Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes)); ?>


        <?php echo e(\Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes)); ?>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale960ae7ad1b1ce9e3596e483505fadc9)): ?>
<?php $attributes = $__attributesOriginale960ae7ad1b1ce9e3596e483505fadc9; ?>
<?php unset($__attributesOriginale960ae7ad1b1ce9e3596e483505fadc9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale960ae7ad1b1ce9e3596e483505fadc9)): ?>
<?php $component = $__componentOriginale960ae7ad1b1ce9e3596e483505fadc9; ?>
<?php unset($__componentOriginale960ae7ad1b1ce9e3596e483505fadc9); ?>
<?php endif; ?>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views/vendor/filament-panels/components/layout/simple.blade.php ENDPATH**/ ?>