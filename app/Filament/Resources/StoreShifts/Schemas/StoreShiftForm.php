<?php

namespace App\Filament\Resources\StoreShifts\Schemas;

use Filament\Schemas\Schema;

class StoreShiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->default(auth()->id()),
                \Filament\Forms\Components\DateTimePicker::make('opened_at')->default(now())->required(),
                \Filament\Forms\Components\DateTimePicker::make('closed_at'),
                \Filament\Forms\Components\TextInput::make('starting_cash')->numeric()->default(0)->required(),
                \Filament\Forms\Components\TextInput::make('ending_cash')->numeric(),
                \Filament\Forms\Components\Select::make('status')->options([
                    'open' => 'Open',
                    'closed' => 'Closed',
                ])->default('open')->required(),
            ]);
    }
}
