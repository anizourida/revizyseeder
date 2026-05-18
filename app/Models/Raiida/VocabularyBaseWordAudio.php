<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyBaseWordAudio extends Model
{
    use HasFactory;

    protected $table = 'vocabulary_base_word_audios';

    protected $guarded = [];

    public function vocabularyItem(): BelongsTo
    {
        return $this->belongsTo(VocabularyItem::class);
    }
}
