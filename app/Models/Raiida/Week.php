<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Week extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(FileAsset::class);
    }
}
