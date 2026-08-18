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
                <h1>{{ \App\Models\Setting::get('store_name', 'EASY POS') }}</h1>
                @if($phone = \App\Models\Setting::get('store_phone'))
                    <p><strong>Phone:</strong> {{ $phone }}</p>
                @endif
                @if($email = \App\Models\Setting::get('store_email'))
                    <p><strong>Email:</strong> {{ $email }}</p>
                @endif
                @if($address = \App\Models\Setting::get('store_address'))
                    <p><strong>Address:</strong> {{ $address }}</p>
                @endif
                @if($reg = \App\Models\Setting::get('business_reg'))
                    <p><strong>Reg No:</strong> {{ $reg }}</p>
                @endif
            </div>
            <div class="doc-title">
                <h2>SHOP LENDS SUMMARY</h2>
                <p>Generated on: {{ now()->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="summary-cards">
            <div class="card">
                <h3>Total Shop Borrowed</h3>
                <p>{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalShopBorrowed, 2) }}</p>
            </div>
            <div class="card">
                <h3>Total Shop Settled</h3>
                <p class="highlight-success">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalShopPaid, 2) }}</p>
            </div>
            <div class="card">
                <h3>Total Shop Outstanding (Lend OS)</h3>
                <p class="highlight-danger">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalShopOutstanding, 2) }}</p>
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
                @forelse($rows as $row)
                    @php
                        $cust = $row['customer'];
                    @endphp
                    <tr>
                        <td><strong>{{ $cust->name }}</strong></td>
                        <td>{{ $cust->phone ?: '-' }}</td>
                        <td>{{ $cust->nic_number ?: '-' }}</td>
                        <td>{{ $cust->email ?: '-' }}</td>
                        <td style="text-align: right;">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($row['total_borrowed'], 2) }}</td>
                        <td style="text-align: right;">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($row['total_paid'], 2) }}</td>
                        <td style="text-align: right; font-weight: bold; color: {{ $row['outstanding'] > 0 ? '#d9534f' : '#28a745' }};">
                            {{ \App\Models\Setting::get('currency', '$') }}{{ number_format($row['outstanding'], 2) }}
                        </td>
                        <td style="text-align: center;">
                            @if($row['outstanding'] > 0)
                                <span class="status-badge status-active">Active Debt</span>
                            @else
                                <span class="status-badge status-settled">Settled</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #999;">No customer lend accounts found in the system.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>{{ \App\Models\Setting::get('receipt_footer', 'Thank you for shopping with us!') }}</p>
            <p style="font-size: 9px; margin-top: 5px; color: #ccc;">Generated by POS System - {{ config('app.name') }}</p>
        </div>
    </div>
    <script>
        window.onload = () => {
            window.print();
        }
    </script>
</body>
</html>
