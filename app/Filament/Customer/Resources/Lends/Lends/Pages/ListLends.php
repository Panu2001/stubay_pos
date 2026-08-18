<?php

namespace App\Filament\Customer\Resources\Lends\Lends\Pages;

use App\Filament\Customer\Resources\Lends\Lends\LendResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLends extends ListRecords
{
    protected static string $resource = LendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
