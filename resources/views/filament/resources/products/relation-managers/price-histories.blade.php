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
            <span class="pos-price-history-count">{{ $histories->count() }} {{ \Illuminate\Support\Str::plural('change', $histories->count()) }}</span>
        </div>

        @if ($histories->isEmpty())
            <div class="pos-price-history-empty">
                No price changes recorded yet.
            </div>
        @else
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
                        @foreach ($histories as $history)
                            <tr>
                                <td>{{ optional($history->created_at)->format('M d, Y h:i A') }}</td>
                                <td class="pos-price-history-money">{{ $currency }}{{ number_format((float) $history->old_cost_price, 2) }}</td>
                                <td class="pos-price-history-money pos-price-history-new">{{ $currency }}{{ number_format((float) $history->new_cost_price, 2) }}</td>
                                <td class="pos-price-history-money">{{ $currency }}{{ number_format((float) $history->old_sell_price, 2) }}</td>
                                <td class="pos-price-history-money pos-price-history-new">{{ $currency }}{{ number_format((float) $history->new_sell_price, 2) }}</td>
                                <td>{{ $history->user?->name ?? 'System' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
