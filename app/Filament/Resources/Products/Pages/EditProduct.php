<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\RelationManagers\StockAdjustmentsRelationManager;
use App\Models\Setting;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\View;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function renderingHasRelationManagers(): void
    {
        $this->activeRelationManager = match ($this->activeRelationManager) {
            '0' => 'stockAdjustments',
            '1' => 'priceHistories',
            default => $this->activeRelationManager,
        };

        parent::renderingHasRelationManagers();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
                View::make('filament.resources.products.relation-managers.product-relations-switcher')
                    ->viewData(fn (): array => [
                        'activeRelation' => $this->getActiveProductRelation(),
                        'stockAdjustmentsUrl' => ProductResource::getUrl('edit', [
                            'record' => $this->getRecord(),
                            'relation' => 'stockAdjustments',
                        ]),
                        'priceHistoriesUrl' => ProductResource::getUrl('edit', [
                            'record' => $this->getRecord(),
                            'relation' => 'priceHistories',
                        ]),
                    ]),
                Livewire::make(StockAdjustmentsRelationManager::class, [
                    'ownerRecord' => $this->getRecord(),
                    'pageClass' => static::class,
                ])
                    ->key(StockAdjustmentsRelationManager::class)
                    ->visible(fn (): bool => $this->getActiveProductRelation() === 'stockAdjustments'),
                View::make('filament.resources.products.relation-managers.price-histories')
                    ->visible(fn (): bool => $this->getActiveProductRelation() === 'priceHistories')
                    ->viewData(fn (): array => [
                        'currency' => Setting::get('currency', '$'),
                        'histories' => $this->getRecord()
                            ->priceHistories()
                            ->with('user')
                            ->latest()
                            ->get(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $hasPrefix = !empty($data['prefix']);

        if ($hasPrefix) {
            $data['brand_id_auto'] = $data['brand_id'] ?? null;
            $data['category_id_auto'] = $data['category_id'] ?? null;
            $data['subcategory_id_auto'] = $data['subcategory_id'] ?? null;
            $data['name_auto'] = $data['name'] ?? null;
            $data['unit_quantity_auto'] = $data['unit_quantity'] ?? null;
            $data['unit_auto'] = $data['unit'] ?? null;
            $data['prefix_auto'] = $data['prefix'] ?? null;
            $data['stock_quantity_auto'] = $data['stock_quantity'] ?? null;
            $data['mfg_date_auto'] = $data['mfg_date'] ?? null;
            $data['expiry_date_auto'] = $data['expiry_date'] ?? null;
            $data['autogenerate_barcode'] = false;
        }

        return $data;
    }

    private ?int $previousStock = null;

    protected function beforeSave(): void
    {
        $this->previousStock = (int) ($this->record->stock_quantity ?? 0);
    }

    protected function afterSave(): void
    {
        if ($this->previousStock !== null) {
            $newStock = (int) ($this->record->stock_quantity ?? 0);
            $delta = $newStock - $this->previousStock;

            if ($delta !== 0) {
                \App\Models\StockAdjustment::withoutEvents(function () use ($delta) {
                    \App\Models\StockAdjustment::create([
                        'product_id' => $this->record->id,
                        'user_id' => auth()->id(),
                        'type' => 'adjustment',
                        'quantity' => $delta,
                        'notes' => 'Manual stock update from Edit Product page',
                    ]);
                });
            }
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->isAutoProduct($data)) {
            $data['brand_id'] = $data['brand_id_auto'] ?? $data['brand_id'] ?? $this->record->brand_id;
            $data['category_id'] = $data['category_id_auto'] ?? $data['category_id'] ?? $this->record->category_id;
            $data['subcategory_id'] = $data['subcategory_id_auto'] ?? $data['subcategory_id'] ?? $this->record->subcategory_id;
            $data['name'] = $data['name_auto'] ?? $data['name'] ?? $this->record->name;
            $data['unit_quantity'] = $data['unit_quantity_auto'] ?? $data['unit_quantity'] ?? $this->record->unit_quantity;
            $data['unit'] = $data['unit_auto'] ?? $data['unit'] ?? $this->record->unit;
            $data['prefix'] = $data['prefix_auto'] ?? $data['prefix'] ?? $this->record->prefix;
            $data['stock_quantity'] = $data['stock_quantity_auto'] ?? $data['stock_quantity'] ?? $this->record->stock_quantity;
            $data['mfg_date'] = $data['mfg_date_auto'] ?? $data['mfg_date'] ?? $this->record->mfg_date;
            $data['expiry_date'] = $data['expiry_date_auto'] ?? $data['expiry_date'] ?? $this->record->expiry_date;
        }

        unset(
            $data['brand_id_auto'],
            $data['category_id_auto'],
            $data['subcategory_id_auto'],
            $data['name_auto'],
            $data['unit_quantity_auto'],
            $data['unit_auto'],
            $data['prefix_auto'],
            $data['autogenerate_barcode'],
            $data['stock_quantity_auto'],
            $data['mfg_date_auto'],
            $data['expiry_date_auto']
        );

        return $data;
    }

    private function isAutoProduct(array $data): bool
    {
        return filled($data['brand_id_auto'] ?? null)
            || filled($data['category_id_auto'] ?? null)
            || filled($data['subcategory_id_auto'] ?? null)
            || filled($data['name_auto'] ?? null)
            || filled($data['prefix_auto'] ?? null);
    }

    private function getActiveProductRelation(): string
    {
        return request()->query('relation', $this->activeRelationManager ?? 'stockAdjustments') === 'priceHistories'
            ? 'priceHistories'
            : 'stockAdjustments';
    }
}
