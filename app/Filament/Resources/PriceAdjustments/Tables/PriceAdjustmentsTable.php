<?php

namespace App\Filament\Resources\PriceAdjustments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class PriceAdjustmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Adjusted By')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('old_stock_price_to')
                    ->label('New Base Price')
                    ->money(\App\Models\Setting::get('currency', 'USD')),
                \Filament\Tables\Columns\TextColumn::make('new_stock_price_to')
                    ->label('New Batch Price')
                    ->money(\App\Models\Setting::get('currency', 'USD')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // Immutable audit log
            ])
            ->toolbarActions([
                // Immutable audit log
            ]);
    }
}
