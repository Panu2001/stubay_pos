<?php

namespace App\Filament\Resources\LoginHistories;

use App\Filament\Resources\LoginHistories\Pages\CreateLoginHistory;
use App\Filament\Resources\LoginHistories\Pages\EditLoginHistory;
use App\Filament\Resources\LoginHistories\Pages\ListLoginHistories;
use App\Filament\Resources\LoginHistories\Pages\ViewLoginHistory;
use App\Filament\Resources\LoginHistories\Schemas\LoginHistoryForm;
use App\Filament\Resources\LoginHistories\Schemas\LoginHistoryInfolist;
use App\Filament\Resources\LoginHistories\Tables\LoginHistoriesTable;
use App\Models\LoginHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LoginHistoryResource extends Resource
{
    protected static ?string $model = LoginHistory::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Store Management';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isManager() ?: false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return LoginHistoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoginHistoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoginHistoriesTable::configure($table);
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
            'index' => ListLoginHistories::route('/'),
            'create' => CreateLoginHistory::route('/create'),
            'view' => ViewLoginHistory::route('/{record}'),
            'edit' => EditLoginHistory::route('/{record}/edit'),
        ];
    }
}
