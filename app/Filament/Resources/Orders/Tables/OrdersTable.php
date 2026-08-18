<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        $currency = \App\Models\Setting::get('currency', '$');

        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'customer']))
            ->columns([
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Processed By')
                    ->placeholder('System')
                    ->searchable(),
                TextColumn::make('user.role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        'cashier' => 'success',
                        default => 'gray',
                    })
                    ->placeholder('System')
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->label('Customer Details')
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->prefix($currency)
                    ->sortable(),
                TextColumn::make('discount')
                    ->prefix($currency)
                    ->sortable(),
                TextColumn::make('tax')
                    ->prefix($currency)
                    ->sortable(),
                TextColumn::make('total')
                    ->prefix($currency)
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Timing Details (Time)')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordAction(\Filament\Actions\ViewAction::class)
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                EditAction::make()->visible(fn() => auth()->user()->isAdmin() || auth()->user()->isManager()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
