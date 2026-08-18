<?php
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

    <style>
        .pos-side-panel-scroll {
            min-height: 0;
            max-height: calc(100vh - 6rem);
            overflow-y: auto;
            overflow-x: hidden;
            overscroll-behavior: contain;
            scrollbar-width: thin;
            scrollbar-color: rgb(75 85 99) transparent;
        }

        .pos-side-panel-scroll::-webkit-scrollbar {
            width: 0.45rem;
        }

        .pos-side-panel-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .pos-side-panel-scroll::-webkit-scrollbar-thumb {
            background: rgb(75 85 99);
            border-radius: 999px;
        }

        .pos-side-panel-scroll::-webkit-scrollbar-thumb:hover {
            background: rgb(107 114 128);
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
    </style>

    <div
        x-data="{
            sidebarExpanded: JSON.parse(localStorage.getItem('posSidebarExpanded') ?? 'true'),
            toggleSidebar() {
                this.sidebarExpanded = ! this.sidebarExpanded
                localStorage.setItem('posSidebarExpanded', JSON.stringify(this.sidebarExpanded))
            },
        }"
        class="pos-dashboard-shell w-full flex"
    >
        <!-- Sidebar -->
        <aside
            x-bind:style="{ width: sidebarExpanded ? '16rem' : '5rem' }"
            class="h-screen sticky top-0 bg-gray-900 border-r border-gray-800 flex flex-col p-3 lg:p-4 shrink-0 transition-all duration-200 ease-in-out"
        >
            <div class="mb-8 flex items-center gap-2 px-2 shrink-0">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center shrink-0">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-white"></i>
                </div>
                <span
                    x-show="sidebarExpanded"
                    x-transition.opacity.duration.150ms
                    class="font-bold text-white truncate"
                >
                    <?php echo e(\App\Models\Setting::get('store_name', 'EASY POS')); ?>

                </span>

                <button
                    type="button"
                    x-on:click="toggleSidebar()"
                    class="ml-auto flex h-8 w-8 items-center justify-center rounded-lg bg-gray-800 text-gray-400 hover:text-white"
                    x-bind:title="sidebarExpanded ? 'Collapse side panel' : 'Expand side panel'"
                >
                    <i x-show="sidebarExpanded" data-lucide="panel-left-close" class="w-4 h-4"></i>
                    <i x-show="! sidebarExpanded" data-lucide="panel-left-open" class="w-4 h-4"></i>
                </button>
            </div>
            
            <nav
                class="pos-side-panel-scroll flex flex-col gap-4 flex-1 pr-1"
            >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = filament()->getNavigation(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="px-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($group->getLabel())): ?>
                            <p
                                x-show="sidebarExpanded"
                                x-transition.opacity.duration.150ms
                                class="text-xs uppercase text-gray-500 font-semibold mb-2 truncate"
                            >
                                <?php echo e($group->getLabel()); ?>

                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="space-y-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group->getItems(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $isActive = $item->isActive();
                                ?>
                                <a
                                    href="<?php echo e($item->getUrl()); ?>"
                                    <?php if($item->shouldOpenUrlInNewTab()): ?> target="_blank" <?php endif; ?>
                                    class="sidebar-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg <?php echo e($isActive ? 'text-emerald-400 bg-emerald-500/10' : 'text-gray-400 hover:text-white hover:bg-gray-800'); ?> text-sm"
                                    x-bind:title="sidebarExpanded ? null : <?php echo \Illuminate\Support\Js::from($item->getLabel())->toHtml() ?>"
                                >
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($icon = $item->getIcon()): ?>
                                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => $icon,'class' => 'w-5 h-5 flex-shrink-0 '.e($isActive ? 'text-emerald-400' : 'text-gray-400').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icon),'class' => 'w-5 h-5 flex-shrink-0 '.e($isActive ? 'text-emerald-400' : 'text-gray-400').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <span
                                        x-show="sidebarExpanded"
                                        x-transition.opacity.duration.150ms
                                        class="truncate"
                                    >
                                        <?php echo e($item->getLabel()); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($badge = $item->getBadge())): ?>
                                        <span
                                            x-show="sidebarExpanded"
                                            x-transition.opacity.duration.150ms
                                            class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded text-xs font-medium <?php echo e($isActive ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-800 text-gray-300'); ?>"
                                        >
                                            <?php echo e($badge); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                
                <!-- Spacer -->
                <div class="pb-10"></div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="pos-dashboard-main flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Top Bar -->
            <header class="flex items-center justify-between px-6 py-4 border-b border-gray-800 bg-gray-900/50">
                <div>
                    <h1 class="font-bold text-white text-xl"><?php echo e($livewire?->getTitle() ?? 'Dashboard'); ?></h1>
                    <p class="text-gray-500 text-xs mt-0.5"><?php echo e(now()->format('l, F j, Y')); ?></p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="relative p-2 rounded-lg bg-gray-800 text-gray-400 hover:text-white">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-bold uppercase">
                        <?php echo e(substr(auth()->user()->name ?? 'A', 0, 1)); ?>

                    </div>
                    
                    <form action="<?php echo e(filament()->getLogoutUrl()); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="p-2 ml-2 rounded-lg bg-gray-800 text-gray-400 hover:text-red-400" title="Logout">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 min-h-0 overflow-auto bg-gray-950 p-4 lg:p-6 text-white">
                <div class="w-full">
                    <?php echo e($slot); ?>

                </div>
            </main>
        </div>
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
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views/vendor/filament-panels/components/layout/index.blade.php ENDPATH**/ ?>