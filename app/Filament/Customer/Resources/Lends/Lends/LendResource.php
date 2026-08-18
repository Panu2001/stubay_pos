<?php

namespace App\Filament\Customer\Resources\Lends\Lends;

use App\Filament\Customer\Resources\Lends\Lends\Pages\CreateLend;
use App\Filament\Customer\Resources\Lends\Lends\Pages\EditLend;
use App\Filament\Customer\Resources\Lends\Lends\Pages\ListLends;
use App\Filament\Customer\Resources\Lends\Lends\Pages\ViewLend;
use App\Filament\Customer\Resources\Lends\Lends\Schemas\LendForm;
use App\Filament\Customer\Resources\Lends\Lends\Schemas\LendInfolist;
use App\Filament\Customer\Resources\Lends\Lends\Tables\LendsTable;
use App\Models\Lend;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LendResource extends Resource
{
    protected static ?string $model = Lend::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;
    protected static ?string $navigationLabel = 'My Lends';
    protected static ?string $pluralLabel = 'My Lends';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('customer_id', auth('customer')->id());
    }

    public static function form(Schema $schema): Schema
    {
        return LendForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LendInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LendsTable::configure($table);
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
            'index' => ListLends::route('/'),
            'view' => ViewLend::route('/{record}'),
        ];
    }
}
