<?php

namespace App\Filament\Site\Resources\Products\Pages;

use App\Filament\Site\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
