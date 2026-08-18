<?php

namespace App\Filament\Resources\Lends\Lends\RelationManagers;

use App\Models\Setting;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerLendsRelationManager extends RelationManager
{
    protected static string $relationship = 'lends';

    protected static ?string $title = 'Lend History';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function infolist(Schema $schema): Schema
    {
        $currency = Setting::get('currency', '$');

        return $schema
            ->components([
                Section::make('Lend Summary')
                    ->extraAttributes(['class' => 'pos-lend-detail-section pos-lend-bill-summary pos-lend-bill-single'])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('order.order_number')
                            ->label('Order ID')
                            ->placeholder('No linked order')
                            ->weight('bold'),
                        TextEntry::make('total_amount')
                            ->label('Lend Amount')
                            ->formatStateUsing(fn ($state) => $currency . number_format((float) $state, 2)),
                        TextEntry::make('order.total')
                            ->label('Total Order Amount')
                            ->placeholder('No linked order total')
                            ->formatStateUsing(fn ($state) => $currency . number_format((float) $state, 2)),
                        TextEntry::make('order.payment_method')
                            ->label('Payment Status')
                            ->badge()
                            ->placeholder('lend')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'lend' => 'Lend',
                                'cash' => 'Cash',
                                'card' => 'Card',
                                'bank_transfer' => 'Bank Transfer',
                                null => 'Lend',
                                default => $state,
                            }),
                        TextEntry::make('created_at')
                            ->label('Date')
                            ->dateTime('M d, Y h:i A'),

                        RepeatableEntry::make('order.items')
                            ->label('Purchased Items & Price Details')
                            ->extraAttributes(['class' => 'pos-lend-items-list'])
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Item')
                                    ->weight('bold'),
                                TextEntry::make('quantity')
                                    ->label('Qty'),
                                TextEntry::make('unit_price')
                                    ->label('Price')
                                    ->formatStateUsing(fn ($state) => $currency . number_format((float) $state, 2)),
                                TextEntry::make('subtotal')
                                    ->label('Total')
                                    ->formatStateUsing(fn ($state) => $currency . number_format((float) $state, 2)),
                            ])
                            ->columns(4)
                            ->columnSpanFull()
                            ->placeholder('No purchased items are linked to this lend.'),
                    ])
                    ->columns(5),
            ]);
    }

    public function table(Table $table): Table
    {
        $currency = Setting::get('currency', '$');

        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order.order_number')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->prefix($currency)
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                BadgeColumn::make('order.payment_method')
                    ->label('Payment Status')
                    ->colors([
                        'warning' => 'lend',
                        'success' => 'cash',
                        'info' => 'card',
                        'gray' => 'bank_transfer',
                    ])
                    ->placeholder('Lend')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'lend' => 'Lend',
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'bank_transfer' => 'Bank Transfer',
                        null => 'Lend',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([])
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->label('Details')
                    ->icon('heroicon-o-shopping-bag')
                    ->modalWidth('7xl'),
            ])
            ->toolbarActions([]);
    }
}
