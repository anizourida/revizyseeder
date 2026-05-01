<?php

namespace App\Models\Queue;

use Illuminate\Database\Eloquent\Model;

/**
 * Minimal Eloquent wrapper for the database queue `jobs` table.
 * Used for dashboard visibility/actions only.
 */
class QueueJob extends Model
{
    protected $table = 'jobs';

    public $timestamps = false;

    protected $guarded = [];

    private ?array $decodedPayload = null;

    public function payloadArray(): array
    {
        if ($this->decodedPayload !== null) {
            return $this->decodedPayload;
        }

        $payload = $this->payload;

        if (is_array($payload)) {
            return $this->decodedPayload = $payload;
        }

        if (! is_string($payload) || $payload === '') {
            return $this->decodedPayload = [];
        }

        try {
            $decoded = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            $decoded = [];
        }

        return $this->decodedPayload = is_array($decoded) ? $decoded : [];
    }

    public function getDisplayNameAttribute(): string
    {
        $payload = $this->payloadArray();

        $display = (string) ($payload['displayName'] ?? '');
        if ($display !== '') {
            return $display;
        }

        $name = (string) ($payload['data']['commandName'] ?? '');
        if ($name !== '') {
            return $name;
        }

        return 'Unknown Job';
    }
}

