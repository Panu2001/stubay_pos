<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\Media;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SyncMediaLibrary extends Command
{
    protected $signature = 'media:sync';
    protected $description = 'Sync all existing images into the Media Library';

    public function handle()
    {
        $this->info('Starting media sync...');

        // 1. Sync from Products
        Product::whereNotNull('image')->each(function ($product) {
            $this->track($product->image);
        });

        // 2. Sync from Categories
        Category::whereNotNull('image')->each(function ($category) {
            $this->track($category->image);
        });

        // 3. Sync from Settings (Logo)
        $logo = Setting::get('store_logo');
        if ($logo) {
            $this->track($logo);
        }

        // 4. Sync all files in 'public/media' folder
        $files = Storage::disk('public')->allFiles('media');
        foreach ($files as $file) {
            $this->track($file);
        }

        $this->info('Media sync completed.');
    }

    protected function track($path)
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return;
        }

        if (Media::where('path', $path)->exists()) {
            return;
        }

        Media::create([
            'name' => basename($path),
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'size' => Storage::disk('public')->size($path),
            'mime_type' => Storage::disk('public')->mimeType($path),
        ]);

        $this->line("Tracked: {$path}");
    }
}
