<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product type')
                    ->tabs([
                        'pre_printed' => Tabs\Tab::make('pre_printed')
                            ->label('Product with Pre-printed Barcode')
                            ->schema([
                                Select::make('brand_id')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(fn (Get $get, ?Product $record) => ! $record && ! self::isAutoProduct($get))
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                        TextInput::make('slug'),
                                    ]),
                                Select::make('category_id')
                                    ->label('Main Category')
                                    ->options(\App\Models\Category::whereNull('parent_id')->pluck('name', 'id'))
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('subcategory_id', null))
                                    ->searchable()
                                    ->preload(),
                                Select::make('subcategory_id')
                                    ->label('Sub Category')
                                    ->options(function (Get $get) {
                                        $categoryId = $get('category_id');
                                        if (! $categoryId) {
                                            return \App\Models\Category::whereNotNull('parent_id')->pluck('name', 'id');
                                        }
                                        return \App\Models\Category::where('parent_id', $categoryId)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('name')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && ! self::isAutoProduct($get)),
                                TextInput::make('unit_quantity')
                                    ->label('Quantity')
                                    ->helperText('Size of a single item (e.g., 1 if 1kg)')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && ! self::isAutoProduct($get))
                                    ->numeric()
                                    ->default(1),
                                Select::make('unit')
                                    ->options([
                                        'kg' => 'Kilogram (kg)',
                                        'g' => 'Gram (g)',
                                        'ml' => 'Milliliter (ml)',
                                        'l' => 'Liter (l)',
                                        'piece' => 'Piece',
                                        'box' => 'Box',
                                        'pkt' => 'Packet',
                                        'bottle' => 'Bottle',
                                    ])
                                    ->required(fn (Get $get, ?Product $record) => ! $record && ! self::isAutoProduct($get))
                                    ->searchable(),
                                TextInput::make('barcode')
                                    ->label('Scan Barcode')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && ! self::isAutoProduct($get))
                                    ->unique(\App\Models\Product::class, 'barcode', ignoreRecord: true),
                                TextInput::make('stock_quantity')
                                    ->label('Initial Stock')
                                    ->helperText('Initial stock quantity upon creation.')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && ! self::isAutoProduct($get))
                                    ->numeric()
                                    ->default(0)
                                    ->hidden(fn (?Product $record) => $record !== null),
                                
                                Placeholder::make('old_stock_display')
                                    ->label('Number of Old Stocks')
                                    ->content(fn (?Product $record) => $record ? (string) ($record->stock_quantity ?? 0) : '-')
                                    ->visible(fn (?Product $record) => $record !== null),
                                    
                                Placeholder::make('new_stock_display')
                                    ->label('Number of New Stocks')
                                    ->content(fn (?Product $record) => $record ? (string) ($record->activeStockBatches()->sum('remaining_quantity') ?? 0) : '-')
                                    ->visible(fn (?Product $record) => $record !== null),
                                DatePicker::make('mfg_date')
                                    ->label('Manufacture Date')
                                    ->native(true),
                                DatePicker::make('expiry_date')
                                    ->label('Expiry Date')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && ! self::isAutoProduct($get))
                                    ->native(true)
                                    ->default(now()->addYear()),
                            ])->columns(2),

                        'auto_generated' => Tabs\Tab::make('auto_generated')
                            ->label('Product without Pre-printed Barcode')
                            ->schema([
                                Select::make('brand_id_auto')
                                    ->label('Brand')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(fn (Get $get, ?Product $record) => ! $record && self::isAutoProduct($get))
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                        TextInput::make('slug'),
                                    ]),
                                Select::make('category_id_auto')
                                    ->label('Main Category')
                                    ->options(\App\Models\Category::whereNull('parent_id')->pluck('name', 'id'))
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('subcategory_id_auto', null))
                                    ->searchable()
                                    ->preload(),
                                Select::make('subcategory_id_auto')
                                    ->label('Sub Category')
                                    ->options(function (Get $get) {
                                        $categoryId = $get('category_id_auto');
                                        if (! $categoryId) {
                                            return \App\Models\Category::whereNotNull('parent_id')->pluck('name', 'id');
                                        }
                                        return \App\Models\Category::where('parent_id', $categoryId)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('name_auto')
                                    ->label('Product Name')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && self::isAutoProduct($get)),
                                TextInput::make('unit_quantity_auto')
                                    ->label('Quantity')
                                    ->helperText('Size of a single item (e.g., 1 if 1kg)')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && self::isAutoProduct($get))
                                    ->numeric()
                                    ->default(1),
                                Select::make('unit_auto')
                                    ->label('Unit')
                                    ->options([
                                        'kg' => 'Kilogram (kg)',
                                        'g' => 'Gram (g)',
                                        'ml' => 'Milliliter (ml)',
                                        'l' => 'Liter (l)',
                                        'piece' => 'Piece',
                                        'box' => 'Box',
                                        'pkt' => 'Packet',
                                        'bottle' => 'Bottle',
                                    ])
                                    ->required(fn (Get $get, ?Product $record) => ! $record && self::isAutoProduct($get))
                                    ->searchable(),
                                TextInput::make('prefix_auto')
                                    ->label('Barcode Prefix')
                                    ->placeholder('e.g. 123')
                                    ->maxLength(3)
                                    ->numeric(),
                                Toggle::make('autogenerate_barcode')
                                    ->label('Autogenerate Barcode')
                                    ->default(true)
                                    ->required(fn (Get $get, ?Product $record) => ! $record && self::isAutoProduct($get)),
                                TextInput::make('stock_quantity_auto')
                                    ->label('Initial Stock')
                                    ->helperText('Initial stock quantity upon creation.')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && self::isAutoProduct($get))
                                    ->numeric()
                                    ->default(0)
                                    ->hidden(fn (?Product $record) => $record !== null),
                                
                                Placeholder::make('old_stock_display_auto')
                                    ->label('Number of Old Stocks')
                                    ->content(fn (?Product $record) => $record ? (string) ($record->stock_quantity ?? 0) : '-')
                                    ->visible(fn (?Product $record) => $record !== null),
                                    
                                Placeholder::make('new_stock_display_auto')
                                    ->label('Number of New Stocks')
                                    ->content(fn (?Product $record) => $record ? (string) ($record->activeStockBatches()->sum('remaining_quantity') ?? 0) : '-')
                                    ->visible(fn (?Product $record) => $record !== null),
                                DatePicker::make('mfg_date_auto')
                                    ->label('Manufacture Date')
                                    ->native(true),
                                DatePicker::make('expiry_date_auto')
                                    ->label('Expiry Date')
                                    ->required(fn (Get $get, ?Product $record) => ! $record && self::isAutoProduct($get))
                                    ->native(true)
                                    ->default(now()->addYear()),
                            ])->columns(2),
                    ]),

                Section::make('Pricing & Other Information')
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix(\App\Models\Setting::get('currency', '$')),
                        TextInput::make('cost_price')
                            ->numeric()
                            ->prefix(\App\Models\Setting::get('currency', '$')),
                        TextInput::make('low_stock_notification')
                            ->label('Low Stock Warning Point')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->helperText('Get notified when stock drops to or below this point.'),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    private static function isAutoProduct(Get $get): bool
    {
        return filled($get('brand_id_auto'))
            || filled($get('category_id_auto'))
            || filled($get('subcategory_id_auto'))
            || filled($get('name_auto'))
            || filled($get('prefix_auto'));
    }
}
