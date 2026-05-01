<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
