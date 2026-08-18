<?php

namespace App\Filament\Customer\Resources\Lends\Lends\Pages;

use App\Filament\Customer\Resources\Lends\Lends\LendResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLend extends EditRecord
{
    protected static string $resource = LendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
