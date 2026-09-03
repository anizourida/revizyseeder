<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VocabularyItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'extracted_at' => 'datetime',
        'revizy_skill_id' => 'int',
        'revizy_unite_id' => 'int',
    ];

    public function audio(): HasOne
    {
        return $this->hasOne(Audio::class);
    }

    public function baseWordAudio(): HasOne
    {
        return $this->hasOne(VocabularyBaseWordAudio::class);
    }

    public function sentences(): HasMany
    {
        return $this->hasMany(VocabularySentence::class, 'vocabulary_item_id');
    }
}
