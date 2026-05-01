<?php

namespace App\Models\Queue;

use Illuminate\Database\Eloquent\Model;

/**
 * Minimal Eloquent wrapper for the `failed_jobs` table.
 * Used for dashboard visibility/actions only.
 */
class FailedQueueJob extends Model
{
    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected $guarded = [];
}

