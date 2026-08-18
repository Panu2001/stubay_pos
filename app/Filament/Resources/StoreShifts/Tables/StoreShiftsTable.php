<?php

namespace App\Filament\Resources\StoreShifts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class StoreShiftsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('user.name')->label('Opened By')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('opened_at')->label('Opened At')->dateTime()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('closed_at')->label('Closed At')->dateTime()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('starting_cash')
                    ->label('Starting Cash')
                    ->prefix(\App\Models\Setting::get('currency', '$'))
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                \Filament\Tables\Columns\TextColumn::make('ending_cash')
                    ->label('Ending Cash')
                    ->prefix(\App\Models\Setting::get('currency', '$'))
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'gray',
                        default => 'primary',
                    }),
            ])
            ->defaultSort('opened_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
