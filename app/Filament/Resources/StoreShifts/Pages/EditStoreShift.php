<?php

namespace App\Filament\Resources\StoreShifts\Pages;

use App\Filament\Resources\StoreShifts\StoreShiftResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStoreShift extends EditRecord
{
    protected static string $resource = StoreShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
