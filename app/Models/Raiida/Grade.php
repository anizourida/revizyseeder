<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }
}
