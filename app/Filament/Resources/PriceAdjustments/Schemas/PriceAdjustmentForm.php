<?php

namespace App\Filament\Resources\PriceAdjustments\Schemas;

use Filament\Schemas\Schema;

class PriceAdjustmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                \Filament\Schemas\Components\Section::make('Select Product')
                    ->schema([
                        \Filament\Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('old_stock_price_from', null);
                                    $set('old_stock_price_to', null);
                                    $set('old_stock_cost_from', null);
                                    $set('old_stock_cost_to', null);
                                    $set('new_stock_price_from', null);
                                    $set('new_stock_price_to', null);
                                    $set('new_stock_cost_from', null);
                                    $set('new_stock_cost_to', null);
                                    return;
                                }

                                $product = \App\Models\Product::find($state);
                                if ($product) {
                                    $set('old_stock_price_from', $product->price);
                                    $set('old_stock_price_to', $product->price);
                                    $set('old_stock_cost_from', $product->cost_price);
                                    $set('old_stock_cost_to', $product->cost_price);

                                    $activeBatch = $product->activeStockBatches()->first();
                                    if ($activeBatch) {
                                        $set('new_stock_price_from', $activeBatch->price);
                                        $set('new_stock_price_to', $activeBatch->price);
                                        $set('new_stock_cost_from', $activeBatch->cost_price);
                                        $set('new_stock_cost_to', $activeBatch->cost_price);
                                    } else {
                                        $set('new_stock_price_from', null);
                                        $set('new_stock_price_to', null);
                                        $set('new_stock_cost_from', null);
                                        $set('new_stock_cost_to', null);
                                    }
                                }
                            }),
                    ]),

                \Filament\Schemas\Components\Tabs::make('Adjustments')
                    ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('Old Stock Prices (Current Base Product)')
                        ->columns(2)
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('old_stock_price_from')
                                ->label('Current Old Selling Price')
                                ->disabled()
                                ->dehydrated(),
                            \Filament\Forms\Components\TextInput::make('old_stock_price_to')
                                ->label('New Old Selling Price')
                                ->numeric()
                                ->required(),
                            \Filament\Forms\Components\TextInput::make('old_stock_cost_from')
                                ->label('Current Old Cost Price')
                                ->disabled()
                                ->dehydrated(),
                            \Filament\Forms\Components\TextInput::make('old_stock_cost_to')
                                ->label('New Old Cost Price')
                                ->numeric()
                                ->required(),
                        ]),

                    \Filament\Schemas\Components\Tabs\Tab::make('New Stock Prices (Waiting Batch)')
                        ->columns(2)
                        ->hidden(fn (callable $get) => $get('new_stock_price_from') === null)
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('new_stock_price_from')
                                ->label('Current New Selling Price')
                                ->disabled()
                                ->dehydrated(),
                            \Filament\Forms\Components\TextInput::make('new_stock_price_to')
                                ->label('New New Selling Price')
                                ->numeric()
                                ->helperText('Leave empty if there is no active new stock.')
                                ->disabled(fn (callable $get) => $get('new_stock_price_from') === null),
                            \Filament\Forms\Components\TextInput::make('new_stock_cost_from')
                                ->label('Current New Cost Price')
                                ->disabled()
                                ->dehydrated(),
                            \Filament\Forms\Components\TextInput::make('new_stock_cost_to')
                                ->label('New New Cost Price')
                                ->numeric()
                                ->disabled(fn (callable $get) => $get('new_stock_cost_from') === null),
                        ])
                ]),
                
                \Filament\Forms\Components\Hidden::make('user_id')->default(fn() => auth()->id()),

                \Filament\Schemas\Components\Section::make('Additional Information')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Reason for Price Adjustment')
                            ->rows(3),
                    ]),
            ]);
    }
}
