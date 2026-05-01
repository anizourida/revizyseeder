<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audio extends Model
{
    use HasFactory;

    protected $table = 'audios';

    protected $guarded = [];

    protected $casts = [
        'vocabulary_item_id' => 'int',
    ];

    public function vocabularyItem(): BelongsTo
    {
        return $this->belongsTo(VocabularyItem::class);
    }
}
