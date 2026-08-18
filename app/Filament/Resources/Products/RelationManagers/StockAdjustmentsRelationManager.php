<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Resources\StockAdjustments\StockAdjustmentResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

class StockAdjustmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockAdjustments';

    protected static ?string $relatedResource = StockAdjustmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('By')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'restock' => 'success',
                        'return' => 'info',
                        'damage', 'return_to_supplier' => 'danger',
                        'adjustment' => 'warning',
                        'replace' => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'restock' => 'Restock',
                        'return' => 'Return',
                        'damage' => 'Damage',
                        'adjustment' => 'Adjustment',
                        'replace' => 'Replace',
                        'return_to_supplier' => 'Return to Supplier',
                        default => $state,
                    }),
                TextColumn::make('quantity')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => match ($record->type) {
                        'damage', 'return_to_supplier' => "-$state",
                        'replace' => "= $state",
                        default => "+$state",
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete stock adjustment?')
                    ->modalDescription('This will undo the stock quantity change created by this adjustment. Restocks can only be deleted if the added batch has not been sold yet.'),
            ]);
    }
}
