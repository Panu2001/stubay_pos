<?php

namespace App\Filament\Resources\PriceAdjustments\Pages;

use App\Filament\Resources\PriceAdjustments\PriceAdjustmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPriceAdjustments extends ListRecords
{
    protected static string $resource = PriceAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
