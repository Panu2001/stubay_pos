<?php

namespace App\Filament\Site\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug'),
                    ]),
                TextInput::make('name')
                    ->required(),
                TextInput::make('barcode')
                    ->required()
                    ->unique(\App\Models\Product::class, 'barcode', ignoreRecord: true)
                    ->autofocus()
                    ->extraInputAttributes(['x-on:keydown.enter.prevent' => ''])
                    ->helperText('Scan with barcode reader. It will safely capture the input without submitting the form.'),
                Textarea::make('description')
                    ->columnSpanFull(),
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
                \Filament\Forms\Components\DatePicker::make('expiry_date')
                    ->label('Expiry Date')
                    ->native(false)
                    ->displayFormat('Y-m-d'),
                \Filament\Forms\Components\DatePicker::make('return_notification_date')
                    ->label('Return Notification Date')
                    ->helperText('When should we notify you to return this product?')
                    ->native(false)
                    ->displayFormat('Y-m-d'),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('image')
                    ->image(),
            ]);
    }
}
