<?php

namespace App\Filament\Customer\Resources\Lends\Lends\Pages;

use App\Filament\Customer\Resources\Lends\Lends\LendResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLend extends ViewRecord
{
    protected static string $resource = LendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
