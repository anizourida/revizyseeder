<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::saving(function ($page) {
            if ($page->isDirty('image_path') && $page->image_path) {
                // 1. Try automated presentation_data path (storage/app/)
                $path = storage_path('app/' . $page->image_path);
                
                // 2. Try manual upload path (storage/app/public/)
                if (!file_exists($path)) {
                    $path = storage_path('app/public/' . $page->image_path);
                }

                if (file_exists($path)) {
                    $page->md5_checksum = md5_file($path);
                    $page->image_size = filesize($path);
                }
            }
        });

        static::updated(function ($page) {
            if ($page->isDirty('page_number') && $page->md5_checksum) {
                // Sync the change to all other records with the same image checksum
                static::where('md5_checksum', $page->md5_checksum)
                    ->where('id', '!=', $page->id)
                    ->update([
                        'page_number' => $page->page_number,
                        'page_number_extraction_method' => $page->page_number_extraction_method,
                    ]);
            }
        });
    }

    public function grade()
    {
        return $this->belongsTo(\App\Models\Raiida\Grade::class);
    }
}
