<?php

namespace App\Filament\Resources\SupplierPurchaseResource\Pages;

use App\Filament\Resources\SupplierPurchaseResource;
use Filament\Resources\Pages\ListRecords;

class ListSupplierPurchases extends ListRecords
{
    protected static string $resource = SupplierPurchaseResource::class;

    protected static ?string $title = 'Supplier Purchases';

    protected function getHeaderActions(): array
    {
        return [
            SupplierPurchaseResource::getCreatePurchaseAction(),
        ];
    }
}
