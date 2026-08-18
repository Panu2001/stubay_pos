<?php

namespace App\Filament\Resources\Lends\Lends;

use App\Filament\Resources\Lends\Lends\Pages\ListLends;
use App\Filament\Resources\Lends\Lends\Pages\CustomerLendHistory;
use App\Filament\Resources\Lends\Lends\Pages\ViewCustomerLends;
use App\Filament\Resources\Lends\Lends\RelationManagers\CustomerLendsRelationManager;
use App\Models\Customer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LendResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'lends';

    protected static ?string $navigationLabel = 'Lends';

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Lends';

    public static function canViewAny(): bool
    {
        return ! auth()->user()->isStockKeeper();
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\Lends\Lends\Tables\LendsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CustomerLendsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLends::route('/'),
            'edit'  => ViewCustomerLends::route('/{record}/lends'),
            'history' => CustomerLendHistory::route('/{record}/lends/history'),
        ];
    }
}
