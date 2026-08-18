<div class="pos-product-relations-switcher">
    <style>
        .pos-product-relations-switcher {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-top: 1.5rem;
        }

        .pos-product-relations-tabs {
            display: inline-flex;
            gap: 0.35rem;
            padding: 0.55rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            background: #18181b;
            box-shadow: 0 12px 34px rgba(0, 0, 0, 0.2);
        }

        .pos-product-relations-tab {
            display: inline-flex;
            align-items: center;
            min-height: 2.75rem;
            padding: 0 1rem;
            border-radius: 8px;
            color: #c4c7d8;
            font-weight: 600;
            text-decoration: none;
        }

        .pos-product-relations-tab:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.04);
        }

        .pos-product-relations-tab.is-active {
            color: #00d39b;
            background: rgba(255, 255, 255, 0.06);
        }
    </style>

    <div class="pos-product-relations-tabs">
        <a
            href="{{ $stockAdjustmentsUrl }}"
            class="pos-product-relations-tab {{ $activeRelation === 'stockAdjustments' ? 'is-active' : '' }}"
        >
            Stock Adjustments
        </a>

        <a
            href="{{ $priceHistoriesUrl }}"
            class="pos-product-relations-tab {{ $activeRelation === 'priceHistories' ? 'is-active' : '' }}"
        >
            Price Histories
        </a>
    </div>
</div>
