<?php

namespace App\Filament\Resources;

use App\Models\CashEntry;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class CashEntryResource extends Resource
{
    protected static ?string $model = CashEntry::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static \UnitEnum|string|null $navigationGroup = 'Inventory';
    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options([
                        'in' => 'Cash In (Deposit)',
                        'out' => 'Cash Out (Withdrawal)',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix(Setting::get('currency', '$')),
                TextInput::make('reason')
                    ->required()
                    ->placeholder('e.g. For tea, For purchasing change, etc.')
                    ->maxLength(255),
                Textarea::make('notes')
                    ->placeholder('Additional details...')
                    ->columnSpanFull(),
                Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('By')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Cash In',
                        'out' => 'Cash Out',
                    }),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => \App\Models\Setting::get('currency', '$') . ' ' . number_format($state, 2))
                    ->sortable(),
                TextColumn::make('reason')
                    ->searchable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'in' => 'Cash In',
                        'out' => 'Cash Out',
                    ]),
            ])
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (CashEntry $record) => route('cash-entry-receipt', $record))
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin()),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->isAdmin()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CashEntryResource\Pages\ManageCashEntries::route('/'),
        ];
    }
}
