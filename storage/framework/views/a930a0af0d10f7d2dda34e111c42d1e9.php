<div class="pos-lend-history-page">
    <style>
        .pos-lend-history-page {
            display: grid;
            gap: 1rem;
            width: 100%;
        }

        .pos-lend-history-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .pos-lend-history-stat,
        .pos-lend-history-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: #18181b;
            box-shadow: 0 14px 38px rgba(0, 0, 0, 0.18);
        }

        .pos-lend-history-stat {
            padding: 1rem;
        }

        .pos-lend-history-stat span {
            display: block;
            color: #a1a1aa;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pos-lend-history-stat strong {
            display: block;
            margin-top: 0.4rem;
            color: #f8fafc;
            font-size: 1.25rem;
        }

        .pos-lend-history-card {
            overflow: hidden;
        }

        .pos-lend-history-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .pos-lend-history-card-header h3 {
            margin: 0;
            color: #f8fafc;
            font-size: 1rem;
            font-weight: 700;
        }

        .pos-lend-history-table-wrap {
            overflow-x: auto;
        }

        .pos-lend-history-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        .pos-lend-history-table th,
        .pos-lend-history-table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #d4d4d8;
            font-size: 0.9rem;
            text-align: left;
            vertical-align: top;
        }

        .pos-lend-history-table th {
            color: #a1a1aa;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pos-lend-history-money {
            color: #f8fafc;
            font-weight: 700;
            white-space: nowrap;
        }

        .pos-lend-history-remaining {
            color: #fb7185;
            font-weight: 800;
            white-space: nowrap;
        }

        .pos-lend-history-paid {
            color: #00d39b;
            font-weight: 800;
            white-space: nowrap;
        }

        .pos-lend-history-badge {
            display: inline-flex;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: rgba(251, 191, 36, 0.12);
            color: #fbbf24;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .pos-lend-history-badge.is-paid {
            background: rgba(0, 211, 155, 0.12);
            color: #00d39b;
        }

        .pos-lend-history-items {
            display: grid;
            gap: 0.25rem;
            min-width: 14rem;
        }

        .pos-lend-history-item {
            color: #c4c7d8;
            font-size: 0.85rem;
        }

        .pos-lend-history-empty {
            padding: 2rem;
            color: #a1a1aa;
            text-align: center;
        }

        @media (max-width: 900px) {
            .pos-lend-history-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>

    <div class="pos-lend-history-summary">
        <div class="pos-lend-history-stat">
            <span>Total Lends</span>
            <strong><?php echo e($currency); ?><?php echo e(number_format((float) $stats['total_lends'], 2)); ?></strong>
        </div>
        <div class="pos-lend-history-stat">
            <span>Total Paid</span>
            <strong><?php echo e($currency); ?><?php echo e(number_format((float) $stats['total_paid'], 2)); ?></strong>
        </div>
        <div class="pos-lend-history-stat">
            <span>Outstanding</span>
            <strong><?php echo e($currency); ?><?php echo e(number_format((float) $stats['outstanding'], 2)); ?></strong>
        </div>
        <div class="pos-lend-history-stat">
            <span>Open Lends</span>
            <strong><?php echo e($stats['open_lends']); ?></strong>
        </div>
    </div>

    <div class="pos-lend-history-card">
        <div class="pos-lend-history-card-header">
            <h3><?php echo e($customer->name); ?> lend history</h3>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lends->isEmpty()): ?>
            <div class="pos-lend-history-empty">
                No lend history recorded for this customer.
            </div>
        <?php else: ?>
            <div class="pos-lend-history-table-wrap">
                <table class="pos-lend-history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $lends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $order = $lend->order;
                                $items = $order ? $order->items : collect();
                                $status = $lend->status ?: 'unpaid';
                                $statusLabel = ucwords(str_replace('_', ' ', $status));
                                $isPaid = $status === 'paid';
                                $remainingAmount = (float) $lend->remaining_amount;
                            ?>
                            <tr>
                                <td><?php echo e(optional($lend->created_at)->format('M d, Y h:i A')); ?></td>
                                <td><?php echo e($order ? $order->order_number : 'No linked order'); ?></td>
                                <td>
                                    <div class="pos-lend-history-items">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <div class="pos-lend-history-item">
                                                <?php echo e($item->name); ?> x <?php echo e($item->quantity); ?>

                                                - <?php echo e($currency); ?><?php echo e(number_format((float) $item->unit_price, 2)); ?>

                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <div class="pos-lend-history-item">No item details</div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </td>
                                <td class="pos-lend-history-money"><?php echo e($currency); ?><?php echo e(number_format((float) $lend->total_amount, 2)); ?></td>
                                <td class="pos-lend-history-paid"><?php echo e($currency); ?><?php echo e(number_format((float) $lend->paid_amount, 2)); ?></td>
                                <td class="<?php echo e($remainingAmount > 0 ? 'pos-lend-history-remaining' : 'pos-lend-history-paid'); ?>">
                                    <?php echo e($currency); ?><?php echo e(number_format($remainingAmount, 2)); ?>

                                </td>
                                <td>
                                    <span class="pos-lend-history-badge <?php echo e($isPaid ? 'is-paid' : ''); ?>">
                                        <?php echo e($statusLabel); ?>

                                    </span>
                                </td>
                                <td><?php echo e($lend->notes ?: '-'); ?></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views\filament\resources\lends\customer-lend-history.blade.php ENDPATH**/ ?>