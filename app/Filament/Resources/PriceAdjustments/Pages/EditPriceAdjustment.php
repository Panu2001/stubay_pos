<?php

namespace App\Filament\Resources\PriceAdjustments\Pages;

use App\Filament\Resources\PriceAdjustments\PriceAdjustmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPriceAdjustment extends EditRecord
{
    protected static string $resource = PriceAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
