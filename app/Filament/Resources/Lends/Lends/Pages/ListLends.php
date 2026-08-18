<?php

namespace App\Filament\Resources\Lends\Lends\Pages;

use App\Filament\Resources\Lends\Lends\LendResource;
use Filament\Resources\Pages\ListRecords;

class ListLends extends ListRecords
{
    protected static string $resource = LendResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
