<div class="fi-resource-relation-manager">
    @if ($this instanceof \App\Filament\Resources\Products\RelationManagers\PriceHistoriesRelationManager)
        @php
            $currency = \App\Models\Setting::get('currency', '$');
            $histories = $this->ownerRecord
                ->priceHistories()
                ->with('user')
                ->latest()
                ->get();
        @endphp

        @include('filament.resources.products.relation-managers.price-histories', [
            'currency' => $currency,
            'histories' => $histories,
        ])
    @else
        {{ $this->content }}
    @endif

    <x-filament-panels::unsaved-action-changes-alert />
</div>
