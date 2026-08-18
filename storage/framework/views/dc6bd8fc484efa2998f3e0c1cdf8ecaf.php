<!DOCTYPE html>
<html>
<head>
    <title>Shop Lends Summary Statement</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
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

        .summary-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .card { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; text-align: center; }
        .card h3 { margin: 0 0 5px 0; font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .card p { margin: 0; font-size: 22px; font-weight: bold; color: #333; }
        .card p.highlight-danger { color: #d9534f; }
        .card p.highlight-success { color: #28a745; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #e9ecef; }
        th { background: #f8f9fa; font-weight: bold; color: #495057; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        tr:hover { background: #fbfbfb; }

        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-settled { background: #d4edda; color: #155724; }
        .status-active { background: #f8d7da; color: #721c24; }

        .footer { margin-top: 60px; text-align: center; color: #999; font-size: 11px; border-top: 1px solid #eee; padding-top: 20px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; background: white; }
            .container { max-width: 100%; }
            .invoice-header { border-bottom: 2px solid #333; }
            .card { border: 1px solid #ccc; background: none; }
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
                <h2>SHOP LENDS SUMMARY</h2>
                <p>Generated on: <?php echo e(now()->format('Y-m-d H:i')); ?></p>
            </div>
        </div>

        <div class="summary-cards">
            <div class="card">
                <h3>Total Shop Borrowed</h3>
                <p><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalShopBorrowed, 2)); ?></p>
            </div>
            <div class="card">
                <h3>Total Shop Settled</h3>
                <p class="highlight-success"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalShopPaid, 2)); ?></p>
            </div>
            <div class="card">
                <h3>Total Shop Outstanding (Lend OS)</h3>
                <p class="highlight-danger"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($totalShopOutstanding, 2)); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>NIC / ID</th>
                    <th>Email</th>
                    <th style="text-align: right;">Total Borrowed</th>
                    <th style="text-align: right;">Total Settled</th>
                    <th style="text-align: right;">Current Outstanding</th>
                    <th style="text-align: center;">Account Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $cust = $row['customer'];
                    ?>
                    <tr>
                        <td><strong><?php echo e($cust->name); ?></strong></td>
                        <td><?php echo e($cust->phone ?: '-'); ?></td>
                        <td><?php echo e($cust->nic_number ?: '-'); ?></td>
                        <td><?php echo e($cust->email ?: '-'); ?></td>
                        <td style="text-align: right;"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($row['total_borrowed'], 2)); ?></td>
                        <td style="text-align: right;"><?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($row['total_paid'], 2)); ?></td>
                        <td style="text-align: right; font-weight: bold; color: <?php echo e($row['outstanding'] > 0 ? '#d9534f' : '#28a745'); ?>;">
                            <?php echo e(\App\Models\Setting::get('currency', '$')); ?><?php echo e(number_format($row['outstanding'], 2)); ?>

                        </td>
                        <td style="text-align: center;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['outstanding'] > 0): ?>
                                <span class="status-badge status-active">Active Debt</span>
                            <?php else: ?>
                                <span class="status-badge status-settled">Settled</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #999;">No customer lend accounts found in the system.</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>

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
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views\lends\shop_print.blade.php ENDPATH**/ ?>