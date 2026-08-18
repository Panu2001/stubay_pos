<?php

namespace App\Filament\Resources\StoreShifts\Pages;

use App\Filament\Resources\StoreShifts\StoreShiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStoreShifts extends ListRecords
{
    protected static string $resource = StoreShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
