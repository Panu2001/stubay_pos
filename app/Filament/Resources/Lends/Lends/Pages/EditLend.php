<?php

namespace App\Filament\Resources\Lends\Lends\Pages;

use App\Filament\Resources\Lends\Lends\LendResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLend extends EditRecord
{
    protected static string $resource = LendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
