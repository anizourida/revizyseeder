<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConjugaisonGrade extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'grade_number' => 'int',
    ];

    public function conjugaisons(): HasMany
    {
        return $this->hasMany(Conjugaison::class, 'grade_id');
    }
}
