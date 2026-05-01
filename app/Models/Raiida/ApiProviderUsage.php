<?php

namespace App\Models\Raiida;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiProviderUsage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'last_synced_at' => 'datetime',
        'requests_count' => 'int',
        'input_tokens_count' => 'int',
        'output_tokens_count' => 'int',
        'total_tokens_count' => 'int',
        'characters_count' => 'int',
        'remote_used' => 'int',
        'remote_limit' => 'int',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ApiProvider::class, 'api_provider_id');
    }
}

