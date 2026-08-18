<?php

namespace App\Filament\Resources\Taxes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TaxForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tax Name')
                    ->placeholder('e.g. VAT, Sales Tax')
                    ->required(),
                TextInput::make('rate')
                    ->label('Tax Rate')
                    ->suffix('%')
                    ->required()
                    ->numeric(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
                Toggle::make('is_default')
                    ->label('Default Tax')
                    ->helperText('This tax will be automatically applied to new sales.')
                    ->required(),
            ]);
    }
}
