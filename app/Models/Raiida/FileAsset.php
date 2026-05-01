<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileAsset extends Model
{
    use HasFactory;

    public const DOWNLOAD_STATE_PENDING = 'pending';
    public const DOWNLOAD_STATE_DOWNLOADING = 'downloading';
    public const DOWNLOAD_STATE_DOWNLOADED = 'downloaded';
    public const DOWNLOAD_STATE_FAILED = 'failed';

    protected $guarded = [];

    protected $casts = [
        'is_downloaded' => 'bool',
        'is_integrity_checked' => 'bool',
        'is_corrupt' => 'bool',
        'is_vocab_extracted' => 'bool',
        'is_presentation_data_extracted' => 'bool',
        'size_bytes' => 'int',
        'vocab_count' => 'int',
        'presentation_slide_count' => 'int',
        'download_progress' => 'int',
        'downloaded_at' => 'datetime',
        'download_started_at' => 'datetime',
        'download_finished_at' => 'datetime',
        'presentation_extracted_at' => 'datetime',
    ];

    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class);
    }
}
