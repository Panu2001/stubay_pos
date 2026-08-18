<div class="pos-price-history-relation">
    <style>
        .pos-price-history-relation {
            width: 100%;
        }

        .pos-price-history-panel {
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: #18181b;
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.22);
        }

        .pos-price-history-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .pos-price-history-title {
            margin: 0;
            color: #f8fafc;
            font-size: 1rem;
            font-weight: 700;
        }

        .pos-price-history-count {
            color: #a5b4fc;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .pos-price-history-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .pos-price-history-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        .pos-price-history-table th,
        .pos-price-history-table td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #d4d4d8;
            font-size: 0.925rem;
            text-align: left;
            white-space: nowrap;
        }

        .pos-price-history-table th {
            color: #a1a1aa;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pos-price-history-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .pos-price-history-money {
            color: #f8fafc;
            font-weight: 700;
        }

        .pos-price-history-new {
            color: #00d39b;
        }

        .pos-price-history-empty {
            padding: 2rem;
            color: #a1a1aa;
            text-align: center;
        }
    </style>

    <div class="pos-price-history-panel">
        <div class="pos-price-history-header">
            <h3 class="pos-price-history-title">Price history</h3>
            <span class="pos-price-history-count"><?php echo e($histories->count()); ?> <?php echo e(\Illuminate\Support\Str::plural('change', $histories->count())); ?></span>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($histories->isEmpty()): ?>
            <div class="pos-price-history-empty">
                No price changes recorded yet.
            </div>
        <?php else: ?>
            <div class="pos-price-history-table-wrap">
                <table class="pos-price-history-table">
                    <thead>
                        <tr>
                            <th>Date &amp; Time</th>
                            <th>Old Cost</th>
                            <th>New Cost</th>
                            <th>Old Price</th>
                            <th>New Price</th>
                            <th>Changed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td><?php echo e(optional($history->created_at)->format('M d, Y h:i A')); ?></td>
                                <td class="pos-price-history-money"><?php echo e($currency); ?><?php echo e(number_format((float) $history->old_cost_price, 2)); ?></td>
                                <td class="pos-price-history-money pos-price-history-new"><?php echo e($currency); ?><?php echo e(number_format((float) $history->new_cost_price, 2)); ?></td>
                                <td class="pos-price-history-money"><?php echo e($currency); ?><?php echo e(number_format((float) $history->old_sell_price, 2)); ?></td>
                                <td class="pos-price-history-money pos-price-history-new"><?php echo e($currency); ?><?php echo e(number_format((float) $history->new_sell_price, 2)); ?></td>
                                <td><?php echo e($history->user?->name ?? 'System'); ?></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views\filament\resources\products\relation-managers\price-histories.blade.php ENDPATH**/ ?>