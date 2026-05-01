<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(Period::class);
    }
}
