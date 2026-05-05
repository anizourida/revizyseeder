<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;
    
    protected static function booted()
    {
        static::addGlobalScope('order_by_code', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->orderBy('code', 'asc');
        });
    }

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
