<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

trait TracksMedia
{
    public static function bootTracksMedia()
    {
        static::saved(function ($model) {
            $imageField = property_exists($model, 'imageField') ? $model->imageField : 'image';
            
            if ($model->isDirty($imageField) && $model->$imageField) {
                $path = $model->$imageField;
                
                // Only track if it's not already in Media
                if (!Media::where('path', $path)->exists()) {
                    Media::create([
                        'name' => basename($path),
                        'path' => $path,
                        'url' => Storage::disk('public')->url($path),
                        'size' => Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 0,
                        'mime_type' => Storage::disk('public')->exists($path) ? Storage::disk('public')->mimeType($path) : 'image/unknown',
                    ]);
                }
            }
        });

        static::deleted(function ($model) {
            $imageField = property_exists($model, 'imageField') ? $model->imageField : 'image';
            $path = $model->$imageField;
            
            if ($path) {
                // We don't necessarily want to delete the Media record if other models might use it
                // But for now, we'll keep it in the library unless manually deleted from there.
            }
        });
    }
}
