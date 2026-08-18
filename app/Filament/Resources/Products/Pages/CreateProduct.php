<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->isAutoProduct($data)) {
            $data['brand_id'] = $data['brand_id_auto'] ?? $data['brand_id'] ?? null;
            $data['category_id'] = $data['category_id_auto'] ?? $data['category_id'] ?? null;
            $data['subcategory_id'] = $data['subcategory_id_auto'] ?? $data['subcategory_id'] ?? null;
            $data['name'] = $data['name_auto'] ?? $data['name'] ?? null;
            $data['unit_quantity'] = $data['unit_quantity_auto'] ?? $data['unit_quantity'] ?? null;
            $data['unit'] = $data['unit_auto'] ?? $data['unit'] ?? null;
            $data['prefix'] = $data['prefix_auto'] ?? $data['prefix'] ?? null;
            $data['stock_quantity'] = $data['stock_quantity_auto'] ?? $data['stock_quantity'] ?? null;
            $data['mfg_date'] = $data['mfg_date_auto'] ?? $data['mfg_date'] ?? null;
            $data['expiry_date'] = $data['expiry_date_auto'] ?? $data['expiry_date'] ?? null;

            if ($data['autogenerate_barcode'] ?? false) {
                $data['barcode'] = null;
            }
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
}
