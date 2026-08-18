<?php

namespace App\Filament\Resources\Lends\Lends\Schemas;

use Filament\Schemas\Schema;

class LendForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number'),
                \Filament\Forms\Components\TextInput::make('total_amount')
                    ->numeric()
                    ->required(),
                \Filament\Forms\Components\TextInput::make('paid_amount')
                    ->numeric()
                    ->default(0)
                    ->required(),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'partially_paid' => 'Partially Paid',
                        'paid' => 'Paid',
                    ])
                    ->default('unpaid')
                    ->disabled()
                    ->dehydrated(),
                \Filament\Forms\Components\DatePicker::make('due_date'),
                \Filament\Forms\Components\Textarea::make('notes')->columnSpanFull(),
            ]);
    }
}
