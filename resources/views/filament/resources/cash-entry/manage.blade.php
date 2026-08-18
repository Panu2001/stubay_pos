<div>
    @livewire(\Filament\Resources\Pages\ManageRecords::class, ['resource' => $resource])

    <div x-data x-on:print-cash-receipt.window="window.open($event.detail.url, '_blank')">
    </div>
</div>
