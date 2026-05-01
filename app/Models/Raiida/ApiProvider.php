<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApiProvider extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'api_key',
        'auth_cookie',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'auth_cookie' => 'encrypted',
        'is_active' => 'bool',
        'metadata' => 'array',
        'monthly_limit' => 'int',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(ApiProviderUsage::class);
    }

    public function currentUsage(): ?ApiProviderUsage
    {
        return $this->usages()
            ->where('period_key', now()->format('Y-m'))
            ->first();
    }
}
