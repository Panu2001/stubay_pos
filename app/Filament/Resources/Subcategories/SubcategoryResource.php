<?php

namespace App\Filament\Resources\Subcategories;

use App\Filament\Resources\Subcategories\Pages\CreateSubcategory;
use App\Filament\Resources\Subcategories\Pages\EditSubcategory;
use App\Filament\Resources\Subcategories\Pages\ListSubcategories;
use App\Filament\Resources\Subcategories\Schemas\SubcategoryForm;
use App\Filament\Resources\Subcategories\Tables\SubcategoriesTable;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubcategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    
    protected static ?string $modelLabel = 'Subcategory';
    
    protected static ?string $navigationLabel = 'Sub Categories';

    protected static ?string $slug = 'subcategories';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNotNull('parent_id');
    }

    public static function form(Schema $schema): Schema
    {
        return SubcategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubcategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubcategories::route('/'),
            'create' => CreateSubcategory::route('/create'),
            'edit' => EditSubcategory::route('/{record}/edit'),
        ];
    }
}
