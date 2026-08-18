<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Setting;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'priceHistories';

    protected static ?string $title = 'Price Histories';

    protected string $view = 'filament.resources.products.relation-managers.price-histories';

    protected function getViewData(): array
    {
        return [
            'currency' => Setting::get('currency', '$'),
            'histories' => $this->ownerRecord
                ->priceHistories()
                ->with('user')
                ->latest()
                ->get(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Read-only, no form needed
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('old_cost_price')
                    ->label('Old Cost')
                    ->formatStateUsing(fn ($state) => Setting::get('currency', '$') . number_format($state, 2)),
                TextColumn::make('new_cost_price')
                    ->label('New Cost')
                    ->formatStateUsing(fn ($state) => Setting::get('currency', '$') . number_format($state, 2))
                    ->weight('bold')
                    ->color(fn ($record) => $record->new_cost_price > $record->old_cost_price ? 'danger' : 'success'),
                TextColumn::make('old_sell_price')
                    ->label('Old Price')
                    ->formatStateUsing(fn ($state) => Setting::get('currency', '$') . number_format($state, 2)),
                TextColumn::make('new_sell_price')
                    ->label('New Price')
                    ->formatStateUsing(fn ($state) => Setting::get('currency', '$') . number_format($state, 2))
                    ->weight('bold')
                    ->color(fn ($record) => $record->new_sell_price > $record->old_sell_price ? 'success' : 'danger'),
                TextColumn::make('user.name')
                    ->label('Changed By')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
