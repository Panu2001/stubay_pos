<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo e($order->order_number); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'IBM Plex Mono', monospace; }
        .dashed { border-top: 2px dashed #555; }
        .dotted-line { border-top: 1px dotted #888; }
        @page { margin: 0; }
        @media print {
            body { background: white !important; margin: 0 !important; padding: 0 !important; }
            .receipt-wrapper { box-shadow: none !important; width: 80mm !important; max-width: 100% !important; margin: 0 auto; padding: 0px; }
            .no-print { display: none !important; }
        }
        body { box-sizing: border-box; }
    </style>
</head>
<body class="h-full bg-neutral-800 flex items-start justify-center overflow-auto p-6">
    <div class="receipt-wrapper w-full max-w-[300px] bg-white text-neutral-900 shadow-2xl relative" style="font-size:11px;">
        <!-- Header -->
        <div class="text-center pt-0 px-3 pb-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo = \App\Models\Setting::get('store_logo')): ?>
                <div class="flex justify-center mb-1">
                    <img src="<?php echo e(asset('storage/' . $logo)); ?>" class="h-9 w-auto" alt="Logo">
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="text-base font-bold tracking-wide leading-tight">
                <?php echo e(\App\Models\Setting::get('store_name', 'Anantha Multishop')); ?>

            </div>
            <div class="text-[9px] text-neutral-500 mt-0.5 leading-normal">
                <?php echo nl2br(e(\App\Models\Setting::get('store_address', "Store Address Line 1\nCity, State ZIP"))); ?>

            </div>
            <div class="text-[9px] text-neutral-500 mt-0.5">
                Tel: <?php echo e(\App\Models\Setting::get('store_phone', 'No Phone Set')); ?>

            </div>
        </div>

        <div class="dashed mx-3 my-1"></div>

        <!-- Receipt Info -->
        <div class="px-3 py-1 text-[9px] text-neutral-600 flex flex-col space-y-0.5">
            <div>
                Date: <span class="text-neutral-800 font-medium"><?php echo e($order->created_at->format('Y-m-d')); ?></span>
            </div>
            <div>
                Time: <span class="text-neutral-800 font-medium"><?php echo e($order->created_at->format('h:i A')); ?></span>
            </div>
            <div>
                Receipt #: <span class="text-neutral-800 font-medium"><?php echo e($order->order_number); ?></span>
            </div>
            <div>
                Cashier: <span class="text-neutral-800 font-medium"><?php echo e($order->user->name ?? 'System'); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->customer): ?>
            <div>
                Customer: <span class="text-neutral-800 font-medium"><?php echo e($order->customer->name); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="dashed mx-3 my-1"></div>

        <!-- Column Headers -->
        <div class="px-3 py-0.5">
            <div class="flex text-[8.5px] font-bold text-neutral-500 uppercase tracking-wider">
                <div class="w-[45%]">Item</div>
                <div class="w-[15%] text-center">Qty</div>
                <div class="w-[15%] text-right">Price</div>
                <div class="w-[25%] text-right">Total</div>
            </div>
        </div>

        <div class="dotted-line mx-3 my-0.5"></div>

        <!-- Items -->
        <div class="px-3 py-0.5 space-y-0.5">
            <?php $currency = \App\Models\Setting::get('currency', 'Rs'); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="flex text-[9.5px] leading-tight">
                <div class="w-[45%] pr-1">
                    <div class="font-medium whitespace-normal break-words leading-tight"><?php echo e($item->custom_name ?: ($item->product ? $item->product->name : 'Deleted Product')); ?></div>
                </div>
                <div class="w-[15%] text-center">
                    <?php echo e($item->quantity); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product && !in_array(strtolower($item->product->unit), ['pice', 'box', 'packet', 'bottle', 'pkt'])): ?>
                        <span class="text-[7.5px]"><?php echo e($item->product->unit); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="w-[15%] text-right text-neutral-500"><?php echo e(number_format($item->unit_price, 2)); ?></div>
                <div class="w-[25%] text-right font-bold"><?php echo e(number_format($item->subtotal, 2)); ?></div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div class="dashed mx-3 my-1.5"></div>

        <!-- Totals -->
        <div class="px-3 space-y-0.5 text-[10px]">
            <div class="flex justify-between text-neutral-600">
                <span>Subtotal</span> <span class="font-medium text-neutral-800"><?php echo e(number_format($order->subtotal, 2)); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->discount > 0): ?>
            <div class="flex justify-between text-neutral-600">
                <span>Discount</span> <span class="font-medium text-neutral-800">-<?php echo e(number_format($order->discount, 2)); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="flex justify-between text-neutral-600">
                <span>Tax (<?php echo e($order->tax_rate ?: \App\Models\Setting::get('tax_rate', 5)); ?>%)</span> <span class="font-medium text-neutral-800"><?php echo e(number_format($order->tax, 2)); ?></span>
            </div>
            
            <div class="dotted-line my-0.5"></div>
            
            <div class="flex justify-between font-bold text-xs py-0.5">
                <span>TOTAL</span> <span><?php echo e($currency); ?> <?php echo e(number_format($order->total, 2)); ?></span>
            </div>
        </div>

        <div class="dotted-line mx-3 my-1"></div>

        <!-- Payment -->
        <div class="px-3 space-y-0.5 text-[10px]">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_method === 'cash' && $order->amount_tendered): ?>
            <div class="flex justify-between text-neutral-600">
                <span>Amount Tendered</span> <span class="font-medium text-neutral-800"><?php echo e(number_format($order->amount_tendered, 2)); ?></span>
            </div>
            <div class="flex justify-between font-bold">
                <span>Change</span> <span><?php echo e(number_format($order->change, 2)); ?></span>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="text-[9px] text-neutral-500 uppercase mt-0.5">
                Payment: <?php echo e($order->payment_method); ?>

            </div>
        </div>

        <div class="dashed mx-3 my-1.5"></div>

        <!-- Footer -->
        <div class="text-center px-3 pb-2 pt-0.5">
            <div class="font-bold text-xs tracking-wide">
                <?php echo e(\App\Models\Setting::get('receipt_footer', 'THANK YOU!')); ?>

            </div>
            <div class="text-[8px] text-neutral-500 mt-0.5">
                Please keep this receipt for your records
            </div>
        </div>

        <!-- Torn edge -->
        <div class="flex w-full overflow-hidden" style="height:8px;">
            <svg width="100%" height="8" preserveAspectRatio="none" viewBox="0 0 320 8">
                <path d="M0,0 Q4,8 8,0 Q12,8 16,0 Q20,8 24,0 Q28,8 32,0 Q36,8 40,0 Q44,8 48,0 Q52,8 56,0 Q60,8 64,0 Q68,8 72,0 Q76,8 80,0 Q84,8 88,0 Q92,8 96,0 Q100,8 104,0 Q108,8 112,0 Q116,8 120,0 Q124,8 128,0 Q132,8 136,0 Q140,8 144,0 Q148,8 152,0 Q156,8 160,0 Q164,8 168,0 Q172,8 176,0 Q180,8 184,0 Q188,8 192,0 Q196,8 200,0 Q204,8 208,0 Q212,8 216,0 Q220,8 224,0 Q228,8 232,0 Q236,8 240,0 Q244,8 248,0 Q252,8 256,0 Q260,8 264,0 Q268,8 272,0 Q276,8 280,0 Q284,8 288,0 Q292,8 296,0 Q300,8 304,0 Q308,8 312,0 Q316,8 320,0" fill="white" />
            </svg>
        </div>

        <div class="no-print absolute top-full left-0 w-full mt-4 flex gap-2">
            <button onclick="window.print()" class="flex-1 bg-neutral-900 text-white font-medium py-2 px-4 rounded shadow hover:bg-neutral-800">
                Print
            </button>
            <button onclick="window.close()" class="flex-1 bg-white text-neutral-900 font-medium py-2 px-4 rounded shadow border border-neutral-200 hover:bg-neutral-50">
                Close
            </button>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views\receipt.blade.php ENDPATH**/ ?>