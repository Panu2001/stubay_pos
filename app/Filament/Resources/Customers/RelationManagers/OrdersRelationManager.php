<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Order Details')
                    ->schema([
                        TextEntry::make('order_number')->weight('bold'),
                        TextEntry::make('created_at')->dateTime()->label('Date & Time'),
                        TextEntry::make('payment_method')->badge(),
                        TextEntry::make('total')->formatStateUsing(fn ($state) => \App\Models\Setting::get('currency', '$') . number_format($state, 2)),
                        TextEntry::make('lend.notes')
                            ->label('Admin Notes (Lend)')
                            ->visible(fn ($record) => $record->payment_method === 'lend')
                            ->columnSpanFull(),
                    ])->columns(4),

                \Filament\Schemas\Components\Section::make('Purchased Items')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('product.name')->label('Product')->weight('bold'),
                                TextEntry::make('quantity')->label('Qty'),
                                TextEntry::make('unit_price')->label('Price')->formatStateUsing(fn ($state) => \App\Models\Setting::get('currency', '$') . number_format($state, 2)),
                                TextEntry::make('subtotal')->label('Total')->formatStateUsing(fn ($state) => \App\Models\Setting::get('currency', '$') . number_format($state, 2)),
                            ])->columns(4)
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_number')
            ->columns([
                TextColumn::make('order_number')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('payment_method')->badge()->sortable(),
                TextColumn::make('total')
                    ->formatStateUsing(fn ($state) => \App\Models\Setting::get('currency', '$') . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('lend.notes')
                    ->label('Notes')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([])
            ->headerActions([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([]);
    }
}
