<?php

namespace App\Filament\Resources\StoreShifts;

use App\Filament\Resources\StoreShifts\Pages\CreateStoreShift;
use App\Filament\Resources\StoreShifts\Pages\EditStoreShift;
use App\Filament\Resources\StoreShifts\Pages\ListStoreShifts;
use App\Filament\Resources\StoreShifts\Pages\ViewStoreShift;
use App\Filament\Resources\StoreShifts\Schemas\StoreShiftForm;
use App\Filament\Resources\StoreShifts\Schemas\StoreShiftInfolist;
use App\Filament\Resources\StoreShifts\Tables\StoreShiftsTable;
use App\Models\StoreShift;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StoreShiftResource extends Resource
{
    protected static ?string $model = StoreShift::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Store Management';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return StoreShiftForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StoreShiftInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StoreShiftsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStoreShifts::route('/'),
            'create' => CreateStoreShift::route('/create'),
            'view' => ViewStoreShift::route('/{record}'),
            'edit' => EditStoreShift::route('/{record}/edit'),
        ];
    }
}
