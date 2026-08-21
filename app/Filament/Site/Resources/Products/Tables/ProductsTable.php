<?php

namespace App\Filament\Site\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Main Category')
                    ->searchable(),
                TextColumn::make('subcategory.name')
                    ->label('Sub Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('barcode')
                    ->searchable(),
                TextColumn::make('price')
                    ->prefix(\App\Models\Setting::get('currency', '$'))
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->prefix(\App\Models\Setting::get('currency', '$'))
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('return_notification_date')
                    ->label('Return Alert')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                ImageColumn::make('image'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('expired')
                    ->label('Already Expired')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder => $query
                        ->whereNotNull('expiry_date')
                        ->where('expiry_date', '<', now()->toDateString())
                    ),
                \Filament\Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring Soon (30 Days)')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder => $query
                        ->whereNotNull('expiry_date')
                        ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
