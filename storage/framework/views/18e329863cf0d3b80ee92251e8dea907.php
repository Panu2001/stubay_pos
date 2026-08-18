<!DOCTYPE html>
<html>
<head>
    <title>Customer Lends Statement - <?php echo e($customer->name); ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .no-print { background: #f8f9fa; padding: 15px; text-align: center; margin-bottom: 30px; border-radius: 8px; border: 1px solid #e9ecef; }
        .no-print button { padding: 10px 20px; font-size: 14px; font-weight: bold; cursor: pointer; border: none; border-radius: 4px; transition: background 0.2s; }
        .btn-print { background: #ff9f1c; color: white; margin-right: 10px; }
        .btn-print:hover { background: #f38f00; }
        .btn-close { background: #6c757d; color: white; }
        .btn-close:hover { background: #5a6268; }
        
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .store-info h1 { margin: 0 0 5px 0; font-size: 26px; color: #ff9f1c; font-weight: 800; text-transform: uppercase; }
        .store-info p { margin: 2px 0; color: #666; }
        .doc-title { text-align: right; }
        .doc-title h2 { margin: 0; font-size: 20px; color: #222; }
        .doc-title p { margin: 5px 0 0 0; color: #888; font-size: 11px; }

        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 30px; }
        .detail-box h3 { margin: 0 0 10px 0; font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 5px; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
        .detail-box p { margin: 4px 0; color: #333; }
        .detail-box p strong { color: #555; width: 100px; display: inline-block; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #e9ecef; }
        th { background: #f8f9fa; font-weight: bold; color: #495057; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        tr:hover { background: #fbfbfb; }

        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-partially_paid { background: #fff3cd; color: #856404; }
        .status-unpaid { background: #f8d7da; color: #721c24; }

        .summary-wrapper { display: flex; justify-content: flex-end; }
        .summary-box { width: 320px; background: #f8f9fa; border-radius: 8px; padding: 15px; border: 1px solid #e9ecef; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
        .summary-row:not(:last-child) { border-bottom: 1px solid #eee; }
        .summary-row.total-due { font-size: 16px; font-weight: bold; color: #d9534f; margin-top: 5px; padding-top: 10px; border-top: 2px dashed #ddd; }

        .footer { margin-top: 60px; text-align: center; color: #999; font-size: 11px; border-top: 1px solid #eee; padding-top: 20px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; background: white; }
            .container { max-width: 100%; }
            .invoice-header { border-bottom: 2px solid #333; }
            .summary-box { border: 1px solid #ccc; background: none; }
            th { background: #eee; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <button onclick="window.print()" class="btn-print">Print Statement</button>
            <button onclick="window.close()" class="btn-close">Close Window</button>
        </div>

        <div class="invoice-header">
            <div class="store-info">
                <h1><?php echo e(\App\Models\Setting::get('store_name', 'EASY POS')); ?></h1>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone = \App\Models\Setting::get('store_phone')): ?>
                    <p><strong>Phone:</strong> <?php echo e($phone); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($email = \App\Models\Setting::get('store_email')): ?>
                    <p><strong>Email:</strong> <?php echo e($email); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($address = \App\Models\Setting::get('store_address')): ?>
                    <p><strong>Address:</strong> <?php echo e($address); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reg = \App\Models\Setting::get('business_reg')): ?>
                    <p><strong>Reg No:</strong> <?php echo e($reg); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="doc-title">
                <h2>LENDS STATEMENT</h2>
                <p>Generated on: <?php echo e(now()->format('Y-m-d H:i')); ?></p>
            </div>
        </div>

        <div class="details-grid">
            <div class="detail-box">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> <?php echo e($customer->name); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customer->nic_number): ?>
                    <p><strong>NIC/ID:</strong> <?php echo e($customer->nic_number); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customer->phone): ?>
                    <p><strong>Phone:</strong> <?php echo e($customer->phone); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customer->email): ?>
                    <p><strong>Email:</strong> <?php echo e($customer->email); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customer->address): ?>
                    <p><strong>Address:</strong> <?php echo e($customer->address); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="detail-box">
                <h3>Account Summary</h3>
                <p><strong>Total Lends:</strong> <?php echo e($totalLends); ?></p>
                <p><strong>Total Value:</strong> <?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalAmount, 2)); ?></p>
                <p><strong>Total Paid:</strong> <?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalPaid, 2)); ?></p>
                <p><strong>Outstanding:</strong> <span style="font-weight: bold; color: <?php echo e($totalOutstanding > 0 ? '#d9534f' : '#28a745'); ?>"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalOutstanding, 2)); ?></span></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Order No.</th>
                    <th>Due Date</th>
                    <th>Notes</th>
                    <th style="text-align: right;">Total Amount</th>
                    <th style="text-align: right;">Paid Amount</th>
                    <th style="text-align: right;">Balance Due</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $lends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $due = $lend->remaining_amount;
                        $items = $lend->order ? $lend->order->items : collect();
                    ?>
                    <tr style="background: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;">
                        <td><?php echo e($lend->created_at->format('Y-m-d H:i')); ?></td>
                        <td><?php echo e($lend->order?->order_number ?? 'N/A'); ?></td>
                        <td><?php echo e($lend->due_date ? \Carbon\Carbon::parse($lend->due_date)->format('Y-m-d') : 'N/A'); ?></td>
                        <td><?php echo e($lend->notes ?: '-'); ?></td>
                        <td style="text-align: right;"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($lend->total_amount, 2)); ?></td>
                        <td style="text-align: right;"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($lend->paid_amount, 2)); ?></td>
                        <td style="text-align: right; color: <?php echo e($due > 0 ? '#d9534f' : '#333'); ?>;">
                            <?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($due, 2)); ?>

                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-<?php echo e($lend->status); ?>">
                                <?php echo e(str_replace('_', ' ', $lend->status)); ?>

                            </span>
                        </td>
                    </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items->isNotEmpty()): ?>
                        <tr>
                            <td colspan="8" style="padding: 5px 15px 15px 30px;">
                                <table style="width: 100%; margin: 0; background: #fff; border: 1px solid #dee2e6; border-radius: 4px;">
                                    <thead>
                                        <tr style="background: #f1f3f5;">
                                            <th style="padding: 6px 10px; font-size: 11px; font-weight: bold; color: #495057; width: 45%; border-bottom: 1px solid #dee2e6;">Product Details</th>
                                            <th style="padding: 6px 10px; font-size: 11px; font-weight: bold; color: #495057; text-align: center; width: 15%; border-bottom: 1px solid #dee2e6;">Quantity</th>
                                            <th style="padding: 6px 10px; font-size: 11px; font-weight: bold; color: #495057; text-align: right; width: 20%; border-bottom: 1px solid #dee2e6;">Unit Price</th>
                                            <th style="padding: 6px 10px; font-size: 11px; font-weight: bold; color: #495057; text-align: right; width: 20%; border-bottom: 1px solid #dee2e6;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <tr>
                                                <td style="padding: 6px 10px; font-size: 11px; border-bottom: 1px solid #e9ecef;"><?php echo e($item->name); ?></td>
                                                <td style="padding: 6px 10px; font-size: 11px; text-align: center; border-bottom: 1px solid #e9ecef;"><?php echo e($item->quantity); ?></td>
                                                <td style="padding: 6px 10px; font-size: 11px; text-align: right; border-bottom: 1px solid #e9ecef;"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($item->unit_price, 2)); ?></td>
                                                <td style="padding: 6px 10px; font-size: 11px; text-align: right; border-bottom: 1px solid #e9ecef;"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($item->subtotal, 2)); ?></td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #999;">No lend records found for this customer.</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>

        <div class="summary-wrapper">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Total Borrowed:</span>
                    <span><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalAmount, 2)); ?></span>
                </div>
                <div class="summary-row">
                    <span>Total Paid:</span>
                    <span><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalPaid, 2)); ?></span>
                </div>
                <div class="summary-row total-due">
                    <span>Total Outstanding:</span>
                    <span><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalOutstanding, 2)); ?></span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><?php echo e(\App\Models\Setting::get('receipt_footer', 'Thank you for shopping with us!')); ?></p>
            <p style="font-size: 9px; margin-top: 5px; color: #ccc;">Generated by POS System - <?php echo e(config('app.name')); ?></p>
        </div>
    </div>
    <script>
        window.onload = () => {
            window.print();
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views\lends\print.blade.php ENDPATH**/ ?>