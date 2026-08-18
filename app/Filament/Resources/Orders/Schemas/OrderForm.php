<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Order Summary')
                    ->schema([
                        TextInput::make('order_number')
                            ->required(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Processed By'),
                        Select::make('customer_id')
                            ->relationship('customer', 'name'),
                        TextInput::make('payment_method')
                            ->disabled(),
                        TextInput::make('created_at')
                            ->label('Order Date')
                            ->disabled(),
                    ])->columns(2)
                        ->columnSpanFull(),

                \Filament\Schemas\Components\Section::make('Order Items')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                TextInput::make('custom_name')
                                    ->label('Product Name')
                                    ->formatStateUsing(fn ($state, ?\App\Models\OrderItem $record) => $state ?: ($record && $record->product ? $record->product->name : 'Deleted Product'))
                                    ->disabled(),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->disabled(),
                                TextInput::make('unit_cost_price')
                                    ->label('Cost Price')
                                    ->prefix(\App\Models\Setting::get('currency', '$'))
                                    ->disabled(),
                                TextInput::make('unit_price')
                                    ->label('Sell Price')
                                    ->prefix(\App\Models\Setting::get('currency', '$'))
                                    ->disabled(),
                                TextInput::make('profit_per_unit')
                                    ->label('Unit Profit')
                                    ->disabled(),
                                TextInput::make('total_profit')
                                    ->label('Total Profit')
                                    ->disabled(),
                                TextInput::make('subtotal')
                                    ->label('Item Subtotal')
                                    ->prefix(\App\Models\Setting::get('currency', '$'))
                                    ->disabled(),
                            ])
                            ->columns(4)
                            ->disabled()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                    ])->columnSpanFull(),

                \Filament\Schemas\Components\Section::make('Financial Details')
                    ->schema([
                        TextInput::make('total_cost')
                            ->label('Total Cost Amount')
                            ->disabled()
                            ->numeric(),
                        TextInput::make('total_profit')
                            ->label('Total Order Profit')
                            ->disabled()
                            ->numeric(),
                        TextInput::make('subtotal')
                            ->required()
                            ->numeric(),
                        TextInput::make('discount')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('tax')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('total')
                            ->required()
                            ->numeric()
                            ->label('Final Total (Revenue)'),
                    ])->columns(2)
                        ->columnSpanFull(),
            ]);
    }
}
