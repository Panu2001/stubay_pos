<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Entry #{{ $entry->id }}</title>
    <style>
        @page { margin: 0; }
        @media print {
            body { width: 80mm; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .receipt { box-shadow: none; max-width: 100%; border: none; padding: 10px; }
        }
        body { background-color: #f3f4f6; display: flex; justify-content: center; padding: 2rem; font-family: 'Courier New', Courier, monospace; }
        .receipt { background: white; padding: 1.5rem; width: 100%; max-width: 80mm; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .divider { border-top: 1px dashed #000; margin: 1rem 0; }
        .text-center { text-align: center; }
        .text-xl { font-size: 1.25rem; }
        .font-bold { font-weight: bold; }
        .w-full { width: 100%; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
    </style>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</head>
<body>
    <div class="receipt text-sm text-black">
        <h2 class="text-center text-xl font-bold mb-1">{{ \App\Models\Setting::get('store_name', 'Store Name') }}</h2>
        <p class="text-center" style="font-size: 14px; margin-bottom: 5px;">CASH TRANSACTION VOUCHER</p>
        
        <div class="divider"></div>
        
        <p style="margin: 5px 0;"><strong>Date:</strong> {{ $entry->created_at->format('Y-m-d H:i:s') }}</p>
        <p style="margin: 5px 0;"><strong>Voucher #:</strong> CE-{{ str_pad($entry->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p style="margin: 5px 0;"><strong>User:</strong> {{ $entry->user->name }}</p>
        
        <div class="divider"></div>
        
        <div class="flex justify-between" style="margin: 10px 0;">
            <span class="font-bold">TYPE:</span>
            <span class="font-bold">{{ $entry->type === 'in' ? 'CASH IN (DEPOSIT)' : 'CASH OUT (WITHDRAWAL)' }}</span>
        </div>
        
        <div class="flex justify-between" style="margin: 10px 0;">
            <span class="font-bold">AMOUNT:</span>
            <span class="font-bold" style="font-size: 18px;">{{ \App\Models\Setting::get('currency', '$') }} {{ number_format($entry->amount, 2) }}</span>
        </div>

        <div class="divider"></div>
        
        <p><strong>Reason:</strong></p>
        <p style="margin-bottom: 20px;">{{ $entry->reason }}</p>

        @if($entry->notes)
            <p><strong>Notes:</strong></p>
            <p style="margin-bottom: 20px; font-size: 12px; color: #444;">{{ $entry->notes }}</p>
        @endif
        
        <div style="margin-top: 40px; border-top: 1px solid #000; padding-top: 5px;" class="text-center">
            <p style="font-size: 10px;">AUTHORIZED SIGNATURE</p>
        </div>

        <button onclick="window.print()" class="no-print" style="margin-top: 20px; width: 100%; background: #000; color: #fff; padding: 10px; border: none; cursor: pointer;">
            Print Voucher
        </button>
        <button onclick="window.close()" class="no-print" style="margin-top: 10px; width: 100%; background: #fff; color: #000; border: 1px solid #000; padding: 10px; cursor: pointer;">
            Close
        </button>
    </div>
</body>
</html>
