<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConjugaisonWeek extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'week_number' => 'int',
    ];

    public function conjugaisons(): HasMany
    {
        return $this->hasMany(Conjugaison::class, 'week_id');
    }
}
