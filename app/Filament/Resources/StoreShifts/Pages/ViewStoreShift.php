<?php

namespace App\Filament\Resources\StoreShifts\Pages;

use App\Filament\Resources\StoreShifts\StoreShiftResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStoreShift extends ViewRecord
{
    protected static string $resource = StoreShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
