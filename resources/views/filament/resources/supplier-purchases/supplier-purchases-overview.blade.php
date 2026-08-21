<div class="pos-supplier-purchases-page" x-data="{
    payModalOpen: false,
    selectedPurchaseId: null,
    selectedPurchaseNumber: '',
    selectedOwed: 0,
    payAmount: 0,
    paymentMethod: 'cash',
    notes: '',
    openPayModal(id, number, owed) {
        this.selectedPurchaseId = id;
        this.selectedPurchaseNumber = number;
        this.selectedOwed = owed;
        this.payAmount = owed;
        this.paymentMethod = 'cash';
        this.notes = '';
        this.payModalOpen = true;
    },
    submitPay() {
        $wire.paySinglePurchase(this.selectedPurchaseId, this.payAmount, this.paymentMethod, this.notes);
        this.payModalOpen = false;
    }
}">
    <style>
        .pos-supplier-purchases-page {
            display: grid;
            gap: 1.25rem;
            width: 100%;
        }

        .pos-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .pos-card,
        .pos-stat-card {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            background: #18181b;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.22);
        }

        .pos-stat-card {
            padding: 1.1rem 1.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .pos-stat-card span {
            display: block;
            color: #a1a1aa;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .pos-stat-card strong {
            display: block;
            margin-top: 0.4rem;
            color: #f8fafc;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .pos-highlight-danger {
            color: #fb7185 !important;
        }

        .pos-highlight-success {
            color: #34d399 !important;
        }

        .pos-highlight-info {
            color: #38bdf8 !important;
        }

        .pos-summary-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(18rem, 0.8fr);
            gap: 1rem;
        }

        .pos-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .pos-card-header h3 {
            margin: 0;
            color: #f8fafc;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .pos-card-muted {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .pos-card-body {
            display: grid;
            gap: 0.85rem;
            padding: 1.15rem 1.25rem;
        }

        .pos-balance-value {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .pos-progress-bar {
            height: 0.75rem;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.07);
        }

        .pos-progress-fill {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #10b981, #06b6d4);
            transition: width 0.3s ease;
        }

        .pos-table-wrap {
            overflow-x: auto;
            border-radius: 0 0 14px 14px;
        }

        .pos-custom-table {
            width: 100%;
            min-width: 820px;
            border-collapse: collapse;
        }

        .pos-custom-table th,
        .pos-custom-table td {
            padding: 0.9rem 1.15rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            font-size: 0.88rem;
            text-align: left;
            vertical-align: middle;
        }

        .pos-custom-table th {
            background: rgba(255, 255, 255, 0.02);
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .pos-custom-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .pos-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.22rem 0.65rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .pos-badge.settled {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .pos-badge.partially_settled {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .pos-badge.not_settled {
            background: rgba(244, 63, 94, 0.15);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.3);
        }

        .pos-items-pill {
            display: grid;
            gap: 0.25rem;
            max-width: 22rem;
        }

        .pos-item-line {
            font-size: 0.82rem;
            color: #94a3b8;
            background: rgba(255, 255, 255, 0.03);
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
        }

        .pos-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .pos-btn-info {
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border-color: rgba(14, 165, 233, 0.3);
        }
        .pos-btn-info:hover {
            background: rgba(14, 165, 233, 0.28);
        }

        .pos-btn-success {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.3);
        }
        .pos-btn-success:hover {
            background: rgba(16, 185, 129, 0.28);
        }

        .pos-btn-danger {
            background: rgba(244, 63, 94, 0.15);
            color: #fb7185;
            border-color: rgba(244, 63, 94, 0.3);
        }
        .pos-btn-danger:hover {
            background: rgba(244, 63, 94, 0.28);
        }

        .pos-empty-box {
            padding: 3rem 1rem;
            color: #94a3b8;
            text-align: center;
        }

        /* Pay Modal Styles */
        .pos-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }

        .pos-modal-content {
            background: #18181b;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .pos-modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pos-modal-body {
            padding: 1.5rem;
            display: grid;
            gap: 1rem;
        }

        .pos-modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: rgba(0, 0, 0, 0.2);
        }

        .pos-form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 0.4rem;
        }

        .pos-input, .pos-select, .pos-textarea {
            width: 100%;
            background: #09090b;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            color: #f8fafc;
            padding: 0.6rem 0.85rem;
            font-size: 0.9rem;
        }

        .pos-input:focus, .pos-select:focus, .pos-textarea:focus {
            outline: none;
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
        }

        @media (max-width: 1024px) {
            .pos-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .pos-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .pos-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Top Statistics Grid -->
    <div class="pos-stats-grid">
        <div class="pos-stat-card">
            <span>Total Purchased</span>
            <strong>{{ $currency }}{{ number_format((float) $stats['total_purchases'], 2) }}</strong>
        </div>
        <div class="pos-stat-card">
            <span>Total Paid</span>
            <strong class="pos-highlight-success">{{ $currency }}{{ number_format((float) $stats['total_paid'], 2) }}</strong>
        </div>
        <div class="pos-stat-card">
            <span>Outstanding Balance</span>
            <strong class="{{ $stats['outstanding'] > 0 ? 'pos-highlight-danger' : 'pos-highlight-success' }}">
                {{ $currency }}{{ number_format((float) $stats['outstanding'], 2) }}
            </strong>
        </div>
        <div class="pos-stat-card">
            <span>Total Purchases / Invoices</span>
            <strong class="pos-highlight-info">{{ $stats['purchase_count'] }} invoices</strong>
        </div>
    </div>

    <!-- Summary Overview Cards -->
    <div class="pos-summary-grid">
        <div class="pos-card">
            <div class="pos-card-header">
                <div>
                    <h3>{{ $supplier->name }}</h3>
                    <div class="pos-card-muted">
                        {{ $supplier->phone ?: 'No phone' }} 
                        @if($supplier->contact_person) • Contact: {{ $supplier->contact_person }} @endif
                    </div>
                </div>
                <span class="pos-badge {{ $supplier->settlement_status }}">
                    {{ ucwords(str_replace('_', ' ', $supplier->settlement_status)) }}
                </span>
            </div>
            <div class="pos-card-body">
                <div class="pos-balance-value {{ $stats['outstanding'] > 0 ? 'pos-highlight-danger' : 'pos-highlight-success' }}">
                    {{ $currency }}{{ number_format((float) $stats['outstanding'], 2) }}
                    <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 500;">remaining debt</span>
                </div>
                <div class="pos-progress-bar">
                    <span class="pos-progress-fill" style="width: {{ (int) $stats['progress'] }}%"></span>
                </div>
                <div class="pos-card-muted">
                    {{ (int) $stats['progress'] }}% paid of {{ $currency }}{{ number_format((float) $stats['total_purchases'], 2) }} total purchases
                </div>
            </div>
        </div>

        <div class="pos-card">
            <div class="pos-card-header">
                <h3>Latest Payment</h3>
            </div>
            <div class="pos-card-body">
                @if ($latestPayment)
                    <div class="pos-balance-value pos-highlight-success" style="font-size: 1.6rem;">
                        {{ $currency }}{{ number_format((float) $latestPayment->amount, 2) }}
                    </div>
                    <div class="pos-card-muted">
                        Paid via <strong style="color: #f8fafc;">{{ ucfirst($latestPayment->payment_method) }}</strong> on {{ optional($latestPayment->created_at)->format('M d, Y h:i A') }}
                    </div>
                    <div class="pos-card-muted" style="font-style: italic;">
                        {{ $latestPayment->notes ?: 'No payment notes recorded.' }}
                    </div>
                @else
                    <div class="pos-card-muted" style="padding: 1rem 0;">No payments recorded yet for this supplier.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Purchases List Table -->
    <div class="pos-card">
        <div class="pos-card-header">
            <h3>Purchases & Invoices History</h3>
            <span class="pos-card-muted">{{ $stats['purchase_count'] }} records found</span>
        </div>

        @if ($purchases->isEmpty())
            <div class="pos-empty-box">
                <div style="font-size: 1.1rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">No purchases found</div>
                <div>Click the "New Purchase" button in the top right to record the first purchase for {{ $supplier->name }}.</div>
            </div>
        @else
            <div class="pos-table-wrap">
                <table class="pos-custom-table">
                    <thead>
                        <tr>
                            <th>Purchase #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total Cost</th>
                            <th>Paid</th>
                            <th>Owed (Debt)</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchases as $purchase)
                            @php
                                $owed = (float) $purchase->owed_amount;
                                $status = $purchase->settlement_status;
                            @endphp
                            <tr>
                                <td style="font-weight: 700; color: #f8fafc;">
                                    {{ $purchase->purchase_number }}
                                </td>
                                <td style="white-space: nowrap; color: #94a3b8;">
                                    {{ optional($purchase->created_at)->format('M d, Y') }}
                                    <div style="font-size: 0.75rem; color: #64748b;">{{ optional($purchase->created_at)->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <div class="pos-items-pill">
                                        @forelse ($purchase->items as $item)
                                            <div class="pos-item-line">
                                                <strong style="color: #f1f5f9;">{{ optional($item->product)->name ?: 'Product #' . $item->product_id }}</strong>
                                                × {{ $item->quantity }} @ {{ $currency }}{{ number_format((float)$item->unit_cost, 2) }}
                                            </div>
                                        @empty
                                            <div style="color: #64748b; font-size: 0.8rem;">No item details</div>
                                        @endforelse
                                    </div>
                                </td>
                                <td style="font-weight: 700; color: #f8fafc; white-space: nowrap;">
                                    {{ $currency }}{{ number_format((float) $purchase->total_amount, 2) }}
                                </td>
                                <td style="color: #34d399; font-weight: 700; white-space: nowrap;">
                                    {{ $currency }}{{ number_format((float) $purchase->paid_amount, 2) }}
                                </td>
                                <td style="font-weight: 700; white-space: nowrap;" class="{{ $owed > 0 ? 'pos-highlight-danger' : 'pos-highlight-success' }}">
                                    {{ $currency }}{{ number_format($owed, 2) }}
                                </td>
                                <td>
                                    <span class="pos-badge {{ $status }}">
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; text-transform: uppercase; font-weight: 600; color: #94a3b8;">
                                        {{ $purchase->payment_method }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.4rem; justify-content: flex-end;">
                                        @if ($owed > 0)
                                            <button type="button" 
                                                    class="pos-btn pos-btn-success"
                                                    @click="openPayModal({{ $purchase->id }}, '{{ $purchase->purchase_number }}', {{ $owed }})">
                                                Pay
                                            </button>
                                        @endif
                                        <a href="{{ route('supplier-purchase.receipt', $purchase) }}" 
                                           target="_blank" 
                                           class="pos-btn pos-btn-info">
                                            Print
                                        </a>
                                        <button type="button" 
                                                class="pos-btn pos-btn-danger"
                                                onclick="confirm('Are you sure you want to delete this purchase? Stock adjustments will be reversed.') || event.stopImmediatePropagation()"
                                                wire:click="deletePurchase({{ $purchase->id }})">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Pay Single Purchase Modal -->
    <div x-show="payModalOpen" 
         x-cloak 
         class="pos-modal-backdrop" 
         @click.self="payModalOpen = false" 
         @keydown.escape.window="payModalOpen = false">
        <div class="pos-modal-content">
            <div class="pos-modal-header">
                <h3 style="margin: 0; color: #f8fafc; font-size: 1.1rem; font-weight: 700;">
                    Pay Purchase <span x-text="selectedPurchaseNumber" style="color: #38bdf8;"></span>
                </h3>
                <button type="button" @click="payModalOpen = false" style="background: none; border: none; color: #94a3b8; font-size: 1.4rem; cursor: pointer;">&times;</button>
            </div>
            <div class="pos-modal-body">
                <div style="background: rgba(255, 255, 255, 0.04); padding: 0.75rem 1rem; border-radius: 8px; display: flex; justify-content: space-between;">
                    <span style="color: #94a3b8; font-size: 0.85rem;">Outstanding Debt:</span>
                    <strong style="color: #fb7185; font-size: 1.1rem;">{{ $currency }}<span x-text="parseFloat(selectedOwed).toFixed(2)"></span></strong>
                </div>

                <div class="pos-form-group">
                    <label>Payment Amount</label>
                    <input type="number" step="0.01" min="0.01" :max="selectedOwed" x-model="payAmount" class="pos-input" />
                </div>

                <div class="pos-form-group">
                    <label>Payment Method</label>
                    <select x-model="paymentMethod" class="pos-select">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <div class="pos-form-group">
                    <label>Payment Notes (Optional)</label>
                    <textarea x-model="notes" rows="2" class="pos-textarea" placeholder="E.g. Paid cash installment..."></textarea>
                </div>
            </div>
            <div class="pos-modal-footer">
                <button type="button" class="pos-btn" style="background: rgba(255, 255, 255, 0.08); color: #cbd5e1;" @click="payModalOpen = false">Cancel</button>
                <button type="button" class="pos-btn pos-btn-success" style="padding: 0.5rem 1.25rem;" @click="submitPay()">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>
