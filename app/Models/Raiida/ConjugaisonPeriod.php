<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConjugaisonPeriod extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'period_number' => 'int',
    ];

    public function conjugaisons(): HasMany
    {
        return $this->hasMany(Conjugaison::class, 'period_id');
    }
}
