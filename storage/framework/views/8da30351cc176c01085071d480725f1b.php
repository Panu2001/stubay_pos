<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode - <?php echo e($product->name); ?></title>
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 10px;
            background-color: #f0f0f0;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(<?php echo e($columns); ?>, 1fr);
            column-gap: <?php echo e($horizontal_gap); ?>mm;
            row-gap: <?php echo e($vertical_gap); ?>mm;
            width: fit-content;
            margin: 0 auto;
        }
        .label {
            width: <?php echo e($width); ?>mm;
            height: <?php echo e($height); ?>mm;
            padding: 0.5mm;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0 0 3px rgba(0,0,0,0.1);
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            overflow: hidden;
            page-break-inside: avoid;
            line-height: 1.1;
        }
        .product-name {
            font-weight: bold;
            font-size: clamp(5.5pt, calc(<?php echo e($height); ?>mm * 0.1), 10pt);
            width: 100%;
            height: 16%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.05;
        }
        .details {
            font-size: clamp(5.5pt, calc(<?php echo e($height); ?>mm * 0.08), 9pt);
            font-weight: bold;
            width: 100%;
            height: 12%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .barcode {
            width: 100%;
            height: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .barcode img {
            max-width: 95%;
            max-height: 75%;
            object-fit: contain;
        }
        .barcode-text {
            font-size: clamp(5.5pt, calc(<?php echo e($height); ?>mm * 0.08), 8.5pt);
            font-weight: bold;
            letter-spacing: 0.2mm;
            margin-top: 0.1mm;
            font-family: monospace;
            height: 20%;
            display: flex;
            align-items: center;
        }
        .dates {
            font-size: clamp(5pt, calc(<?php echo e($height); ?>mm * 0.08), 8pt);
            font-weight: bold;
            width: 100%;
            height: 15%;
            display: flex;
            justify-content: center;
            gap: 2mm;
            align-items: center;
            border-top: 0.05mm solid #eee;
            padding-top: 0.1mm;
        }
        .dates div {
            white-space: nowrap;
        }
        @media print {
            body { background: none; padding: 0; }
            .label { box-shadow: none; border: 0.1mm solid #eee; }
            .actions { display: none; }
            .grid-container { 
                column-gap: <?php echo e($horizontal_gap); ?>mm; 
                row-gap: <?php echo e($vertical_gap); ?>mm; 
            }
        }
        .actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        button {
            padding: 10px 20px;
            background: #4F46E5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="actions">
        <button onclick="window.print()">Print Label</button>
        <button onclick="window.close(); setTimeout(function(){ if(!window.closed) { window.location.href = '/admin/products'; } }, 100);" style="background: #6B7280; margin-left: 10px;">Close</button>
    </div>

    <div class="grid-container">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < $copies; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="label">
                <div class="product-name">
                    <?php echo e($product->name); ?>

                </div>
                
                <div class="details">
                    Price: <?php echo e(\App\Models\Setting::get('currency', '$')); ?> <?php echo e(number_format($product->price, 2)); ?> | <?php echo e($product->unit_quantity); ?> <?php echo e($product->unit); ?>

                </div>

                <div class="barcode">
                    <img src="data:image/png;base64,<?php echo e($barcode); ?>" alt="barcode">
                    <div class="barcode-text"><?php echo e($product->barcode); ?></div>
                </div>

                <div class="dates">
                    <div>MFG: <?php echo e($product->mfg_date); ?></div>
                    <div>EXP: <?php echo e($product->expiry_date); ?></div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <script>
        window.onload = function() { 
            // window.print(); 
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\kanes\OneDrive\Documents\pos\resources\views\products\print.blade.php ENDPATH**/ ?>