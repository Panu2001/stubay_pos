<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode - {{ $product->name }}</title>
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
            grid-template-columns: repeat({{ $columns }}, 1fr);
            column-gap: {{ $horizontal_gap }}mm;
            row-gap: {{ $vertical_gap }}mm;
            width: fit-content;
            margin: 0 auto;
        }
        .label {
            width: {{ $width }}mm;
            height: {{ $height }}mm;
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
            font-size: clamp(5.5pt, calc({{ $height }}mm * 0.1), 10pt);
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
            font-size: clamp(5.5pt, calc({{ $height }}mm * 0.08), 9pt);
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
            font-size: clamp(5.5pt, calc({{ $height }}mm * 0.08), 8.5pt);
            font-weight: bold;
            letter-spacing: 0.2mm;
            margin-top: 0.1mm;
            font-family: monospace;
            height: 20%;
            display: flex;
            align-items: center;
        }
        .dates {
            font-size: clamp(5pt, calc({{ $height }}mm * 0.08), 8pt);
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
                column-gap: {{ $horizontal_gap }}mm; 
                row-gap: {{ $vertical_gap }}mm; 
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
        @for ($i = 0; $i < $copies; $i++)
            <div class="label">
                <div class="product-name">
                    {{ $product->name }}
                </div>
                
                <div class="details">
                    Price: {{ \App\Models\Setting::get('currency', '$') }} {{ number_format($product->price, 2) }} | {{ $product->unit_quantity }} {{ $product->unit }}
                </div>

                <div class="barcode">
                    <img src="data:image/png;base64,{{ $barcode }}" alt="barcode">
                    <div class="barcode-text">{{ $product->barcode }}</div>
                </div>

                <div class="dates">
                    <div>MFG: {{ $product->mfg_date }}</div>
                    <div>EXP: {{ $product->expiry_date }}</div>
                </div>
            </div>
        @endfor
    </div>

    <script>
        window.onload = function() { 
            // window.print(); 
        }
    </script>
</body>
</html>
