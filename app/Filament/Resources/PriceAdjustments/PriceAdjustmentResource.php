<?php

namespace App\Filament\Resources\PriceAdjustments;

use App\Filament\Resources\PriceAdjustments\Pages\CreatePriceAdjustment;
use App\Filament\Resources\PriceAdjustments\Pages\EditPriceAdjustment;
use App\Filament\Resources\PriceAdjustments\Pages\ListPriceAdjustments;
use App\Filament\Resources\PriceAdjustments\Schemas\PriceAdjustmentForm;
use App\Filament\Resources\PriceAdjustments\Tables\PriceAdjustmentsTable;
use App\Models\PriceAdjustment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PriceAdjustmentResource extends Resource
{
    protected static ?string $model = PriceAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PriceAdjustmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PriceAdjustmentsTable::configure($table);
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
            'index' => ListPriceAdjustments::route('/'),
            'create' => CreatePriceAdjustment::route('/create'),
            'edit' => EditPriceAdjustment::route('/{record}/edit'),
        ];
    }
}
