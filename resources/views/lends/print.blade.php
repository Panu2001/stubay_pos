<!DOCTYPE html>
<html>
<head>
    <title>Customer Lends Statement - {{ $customer->name }}</title>
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
                <h2>LENDS STATEMENT</h2>
                <p>Generated on: {{ now()->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="details-grid">
            <div class="detail-box">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> {{ $customer->name }}</p>
                @if($customer->nic_number)
                    <p><strong>NIC/ID:</strong> {{ $customer->nic_number }}</p>
                @endif
                @if($customer->phone)
                    <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                @endif
                @if($customer->email)
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                @endif
                @if($customer->address)
                    <p><strong>Address:</strong> {{ $customer->address }}</p>
                @endif
            </div>
            <div class="detail-box">
                <h3>Account Summary</h3>
                <p><strong>Total Lends:</strong> {{ $totalLends }}</p>
                <p><strong>Total Value:</strong> {{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalAmount, 2) }}</p>
                <p><strong>Total Paid:</strong> {{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalPaid, 2) }}</p>
                <p><strong>Outstanding:</strong> <span style="font-weight: bold; color: {{ $totalOutstanding > 0 ? '#d9534f' : '#28a745' }}">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalOutstanding, 2) }}</span></p>
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
                @forelse($lends as $lend)
                    @php
                        $due = $lend->remaining_amount;
                        $items = $lend->order ? $lend->order->items : collect();
                    @endphp
                    <tr style="background: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;">
                        <td>{{ $lend->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $lend->order?->order_number ?? 'N/A' }}</td>
                        <td>{{ $lend->due_date ? \Carbon\Carbon::parse($lend->due_date)->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ $lend->notes ?: '-' }}</td>
                        <td style="text-align: right;">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($lend->total_amount, 2) }}</td>
                        <td style="text-align: right;">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($lend->paid_amount, 2) }}</td>
                        <td style="text-align: right; color: {{ $due > 0 ? '#d9534f' : '#333' }};">
                            {{ \App\Models\Setting::get('currency', '$') }}{{ number_format($due, 2) }}
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-{{ $lend->status }}">
                                {{ str_replace('_', ' ', $lend->status) }}
                            </span>
                        </td>
                    </tr>
                    @if($items->isNotEmpty())
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
                                        @foreach($items as $item)
                                            <tr>
                                                <td style="padding: 6px 10px; font-size: 11px; border-bottom: 1px solid #e9ecef;">{{ $item->name }}</td>
                                                <td style="padding: 6px 10px; font-size: 11px; text-align: center; border-bottom: 1px solid #e9ecef;">{{ $item->quantity }}</td>
                                                <td style="padding: 6px 10px; font-size: 11px; text-align: right; border-bottom: 1px solid #e9ecef;">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($item->unit_price, 2) }}</td>
                                                <td style="padding: 6px 10px; font-size: 11px; text-align: right; border-bottom: 1px solid #e9ecef;">{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #999;">No lend records found for this customer.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary-wrapper">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Total Borrowed:</span>
                    <span>{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalAmount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Total Paid:</span>
                    <span>{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalPaid, 2) }}</span>
                </div>
                <div class="summary-row total-due">
                    <span>Total Outstanding:</span>
                    <span>{{ \App\Models\Setting::get('currency', '$') }}{{ number_format($totalOutstanding, 2) }}</span>
                </div>
            </div>
        </div>

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
