<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->withSum(['stockBatches as new_stock_volume' => fn ($query) => $query->where('remaining_quantity', '>', 0)], 'remaining_quantity'))
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([50])
            ->searchPlaceholder('Search product name or barcode')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('barcode')
                    ->label('Barcode')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '';
                        try {
                            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                            $barcode = base64_encode($generator->getBarcode($state, $generator::TYPE_CODE_128));
                            return view('filament.tables.columns.barcode', ['barcode' => $barcode, 'state' => $state]);
                        } catch (\Exception $e) {
                            return new HtmlString(e($state));
                        }
                    })
                    ->html()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->prefix(\App\Models\Setting::get('currency', '$'))
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->prefix(\App\Models\Setting::get('currency', '$'))
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->label('Total Stock')
                    ->formatStateUsing(fn ($state) => (string) $state)
                    ->sortable()
                    ->badge()
                    ->color(fn (mixed $state, \App\Models\Product $record): string => match (true) {
                        $state <= $record->low_stock_notification => 'danger',
                        $state <= ($record->low_stock_notification + 10) => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('old_stock_volume')
                    ->label('Old Stock')
                    ->getStateUsing(fn (\App\Models\Product $record) => max(0, (int) $record->stock_quantity - (int) ($record->new_stock_volume ?? 0)))
                    ->formatStateUsing(fn ($state) => (string) $state)
                    ->badge()
                    ->color(fn ($state): string => ((int) $state) > 0 ? 'warning' : 'gray'),
                TextColumn::make('new_stock_volume')
                    ->label('New Stock')
                    ->formatStateUsing(fn ($state) => (string) ((int) ($state ?? 0)))
                    ->badge()
                    ->color(fn ($state): string => ((int) ($state ?? 0)) > 0 ? 'info' : 'gray')
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->date()
                    ->description(fn (\App\Models\Product $record): string => $record->expiry_date && \Carbon\Carbon::parse($record->expiry_date) <= now()->addDays(30) ? 'Expires ' . \Carbon\Carbon::parse($record->expiry_date)->diffForHumans(['parts' => 2]) : '')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder => $query->whereColumn('stock_quantity', '<=', 'low_stock_notification')),
                \Filament\Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring Soon')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder => $query->whereNotNull('expiry_date')->where('expiry_date', '<=', now()->addDays(30))),
            ], layout: FiltersLayout::Dropdown)
            ->recordActions([
                \Filament\Actions\Action::make('print')
                    ->label('Print Barcode')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('width')
                            ->label('Label Width (mm)')
                            ->numeric()
                            ->default(40)
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('height')
                            ->label('Label Height (mm)')
                            ->numeric()
                            ->default(25)
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('copies')
                            ->label('Number of Copies')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1),
                        \Filament\Forms\Components\TextInput::make('columns')
                            ->label('Columns')
                            ->numeric()
                            ->default(2)
                            ->required()
                            ->minValue(1),
                        \Filament\Forms\Components\TextInput::make('horizontal_gap')
                            ->label('Horizontal Gap (mm)')
                            ->numeric()
                            ->step(0.1)
                            ->default(2.5)
                            ->required()
                            ->minValue(0),
                        \Filament\Forms\Components\TextInput::make('vertical_gap')
                            ->label('Vertical Gap (mm)')
                            ->numeric()
                            ->step(0.1)
                            ->default(3)
                            ->required()
                            ->minValue(0),
                    ])
                    ->action(function (\App\Models\Product $record, array $data) {
                        return redirect()->route('product.print', [
                            'product' => $record,
                            'width' => $data['width'],
                            'height' => $data['height'],
                            'copies' => $data['copies'],
                            'columns' => $data['columns'],
                            'horizontal_gap' => $data['horizontal_gap'],
                            'vertical_gap' => $data['vertical_gap'],
                        ]);
                    })
                    ->openUrlInNewTab(),
                EditAction::make()->visible(fn() => auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStockKeeper()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
