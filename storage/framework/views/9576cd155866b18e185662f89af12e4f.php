<div class="fi-resource-relation-manager">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this instanceof \App\Filament\Resources\Products\RelationManagers\PriceHistoriesRelationManager): ?>
        <?php
            $currency = \App\Models\Setting::get('currency', '$');
            $histories = $this->ownerRecord
                ->priceHistories()
                ->with('user')
                ->latest()
                ->get();
        ?>

        <?php echo $__env->make('filament.resources.products.relation-managers.price-histories', [
            'currency' => $currency,
            'histories' => $histories,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>
        <?php echo e($this->content); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal29f738301ffa464f2646caa32428c50f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal29f738301ffa464f2646caa32428c50f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.unsaved-action-changes-alert','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::unsaved-action-changes-alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal29f738301ffa464f2646caa32428c50f)): ?>
<?php $attributes = $__attributesOriginal29f738301ffa464f2646caa32428c50f; ?>
<?php unset($__attributesOriginal29f738301ffa464f2646caa32428c50f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal29f738301ffa464f2646caa32428c50f)): ?>
<?php $component = $__componentOriginal29f738301ffa464f2646caa32428c50f; ?>
<?php unset($__componentOriginal29f738301ffa464f2646caa32428c50f); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views/vendor/filament-panels/resources/relation-manager.blade.php ENDPATH**/ ?>