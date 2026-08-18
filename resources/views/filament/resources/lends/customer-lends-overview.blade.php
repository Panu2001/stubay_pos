<div class="pos-customer-lends-page">
    <style>
        .pos-customer-lends-page {
            display: grid;
            gap: 1rem;
            width: 100%;
        }

        .pos-customer-lends-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .pos-customer-lends-card,
        .pos-customer-lends-stat {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: #18181b;
            box-shadow: 0 14px 38px rgba(0, 0, 0, 0.18);
        }

        .pos-customer-lends-stat {
            padding: 1rem;
        }

        .pos-customer-lends-stat span {
            display: block;
            color: #a1a1aa;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pos-customer-lends-stat strong {
            display: block;
            margin-top: 0.4rem;
            color: #f8fafc;
            font-size: 1.25rem;
        }

        .pos-customer-lends-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(18rem, 0.9fr);
            gap: 1rem;
        }

        .pos-customer-lends-card {
            overflow: hidden;
        }

        .pos-customer-lends-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .pos-customer-lends-card-header h3 {
            margin: 0;
            color: #f8fafc;
            font-size: 1rem;
            font-weight: 700;
        }

        .pos-customer-lends-muted {
            color: #a1a1aa;
            font-size: 0.9rem;
        }

        .pos-customer-lends-body {
            display: grid;
            gap: 0.8rem;
            padding: 1rem 1.25rem;
        }

        .pos-customer-lends-balance {
            color: #fb7185;
            font-size: 2rem;
            font-weight: 800;
        }

        .pos-customer-lends-progress {
            height: 0.7rem;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
        }

        .pos-customer-lends-progress span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #00d39b;
        }

        .pos-customer-lends-table-wrap {
            overflow-x: auto;
        }

        .pos-customer-lends-table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        .pos-customer-lends-table th,
        .pos-customer-lends-table td {
            padding: 0.82rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #d4d4d8;
            font-size: 0.9rem;
            text-align: left;
            vertical-align: top;
        }

        .pos-customer-lends-table th {
            color: #a1a1aa;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pos-customer-lends-money {
            color: #f8fafc;
            font-weight: 700;
            white-space: nowrap;
        }

        .pos-customer-lends-paid {
            color: #00d39b;
            font-weight: 800;
            white-space: nowrap;
        }

        .pos-customer-lends-due {
            color: #fb7185;
            font-weight: 800;
            white-space: nowrap;
        }

        .pos-customer-lends-badge {
            display: inline-flex;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: rgba(251, 191, 36, 0.12);
            color: #fbbf24;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .pos-customer-lends-badge.is-paid {
            background: rgba(0, 211, 155, 0.12);
            color: #00d39b;
        }

        .pos-customer-lends-items {
            display: grid;
            gap: 0.2rem;
            min-width: 12rem;
        }

        .pos-customer-lends-item {
            color: #c4c7d8;
            font-size: 0.84rem;
        }

        .pos-customer-lends-empty {
            padding: 1.5rem;
            color: #a1a1aa;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .pos-customer-lends-stats,
            .pos-customer-lends-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .pos-customer-lends-stats,
            .pos-customer-lends-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="pos-customer-lends-stats">
        <div class="pos-customer-lends-stat">
            <span>Total Lends</span>
            <strong>{{ $currency }}{{ number_format((float) $stats['total_lends'], 2) }}</strong>
        </div>
        <div class="pos-customer-lends-stat">
            <span>Total Paid</span>
            <strong>{{ $currency }}{{ number_format((float) $stats['total_paid'], 2) }}</strong>
        </div>
        <div class="pos-customer-lends-stat">
            <span>Outstanding</span>
            <strong>{{ $currency }}{{ number_format((float) $stats['outstanding'], 2) }}</strong>
        </div>
        <div class="pos-customer-lends-stat">
            <span>Open Lends</span>
            <strong>{{ $stats['open_lends'] }}</strong>
        </div>
    </div>

    <div class="pos-customer-lends-grid">
        <div class="pos-customer-lends-card">
            <div class="pos-customer-lends-card-header">
                <h3>{{ $customer->name }} balance</h3>
                <span class="pos-customer-lends-muted">{{ $stats['lend_count'] }} records</span>
            </div>
            <div class="pos-customer-lends-body">
                <div class="pos-customer-lends-balance">
                    {{ $currency }}{{ number_format((float) $stats['outstanding'], 2) }}
                </div>
                <div class="pos-customer-lends-progress">
                    <span style="width: {{ (int) $stats['progress'] }}%"></span>
                </div>
                <div class="pos-customer-lends-muted">
                    {{ (int) $stats['progress'] }}% settled from {{ $currency }}{{ number_format((float) $stats['total_lends'], 2) }}
                </div>
            </div>
        </div>

        <div class="pos-customer-lends-card">
            <div class="pos-customer-lends-card-header">
                <h3>Latest settlement</h3>
            </div>
            <div class="pos-customer-lends-body">
                @if ($latestSettlement)
                    <div class="pos-customer-lends-paid">
                        {{ $currency }}{{ number_format((float) $latestSettlement->amount, 2) }}
                    </div>
                    <div class="pos-customer-lends-muted">
                        {{ optional($latestSettlement->created_at)->format('M d, Y h:i A') }}
                    </div>
                    <div class="pos-customer-lends-muted">
                        {{ $latestSettlement->notes ?: 'No notes' }}
                    </div>
                @else
                    <div class="pos-customer-lends-muted">No settlement recorded yet.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="pos-customer-lends-card">
        <div class="pos-customer-lends-card-header">
            <h3>Outstanding lends</h3>
        </div>

        @if ($openLends->isEmpty())
            <div class="pos-customer-lends-empty">No outstanding lends for this customer.</div>
        @else
            <div class="pos-customer-lends-table-wrap">
                <table class="pos-customer-lends-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($openLends as $lend)
                            @php
                                $order = $lend->order;
                                $items = $order ? $order->items : collect();
                                $status = $lend->status ?: 'unpaid';
                            @endphp
                            <tr>
                                <td>{{ optional($lend->created_at)->format('M d, Y') }}</td>
                                <td>{{ $order ? $order->order_number : 'No linked order' }}</td>
                                <td>
                                    <div class="pos-customer-lends-items">
                                        @forelse ($items as $item)
                                            <div class="pos-customer-lends-item">
                                                {{ $item->name }} x {{ $item->quantity }}
                                            </div>
                                        @empty
                                            <div class="pos-customer-lends-item">No item details</div>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="pos-customer-lends-money">{{ $currency }}{{ number_format((float) $lend->total_amount, 2) }}</td>
                                <td class="pos-customer-lends-paid">{{ $currency }}{{ number_format((float) $lend->paid_amount, 2) }}</td>
                                <td class="pos-customer-lends-due">{{ $currency }}{{ number_format((float) $lend->remaining_amount, 2) }}</td>
                                <td>
                                    <span class="pos-customer-lends-badge">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="pos-customer-lends-card">
        <div class="pos-customer-lends-card-header">
            <h3>Recent lend history</h3>
        </div>

        @if ($recentLends->isEmpty())
            <div class="pos-customer-lends-empty">No lend records found.</div>
        @else
            <div class="pos-customer-lends-table-wrap">
                <table class="pos-customer-lends-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentLends as $lend)
                            @php
                                $order = $lend->order;
                                $status = $lend->status ?: 'unpaid';
                                $isPaid = $status === 'paid';
                                $remainingAmount = (float) $lend->remaining_amount;
                            @endphp
                            <tr>
                                <td>{{ optional($lend->created_at)->format('M d, Y') }}</td>
                                <td>{{ $order ? $order->order_number : 'No linked order' }}</td>
                                <td class="pos-customer-lends-money">{{ $currency }}{{ number_format((float) $lend->total_amount, 2) }}</td>
                                <td class="pos-customer-lends-paid">{{ $currency }}{{ number_format((float) $lend->paid_amount, 2) }}</td>
                                <td class="{{ $remainingAmount > 0 ? 'pos-customer-lends-due' : 'pos-customer-lends-paid' }}">
                                    {{ $currency }}{{ number_format($remainingAmount, 2) }}
                                </td>
                                <td>
                                    <span class="pos-customer-lends-badge {{ $isPaid ? 'is-paid' : '' }}">
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td>{{ $lend->notes ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
