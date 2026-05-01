<?php

namespace App\Support\RevizySeeder;

use Illuminate\Support\Facades\Cache;

final class WorkflowState
{
    public const PAUSED_CACHE_KEY = 'revizyseeder:workflows_paused';

    public static function workflowQueue(): string
    {
        return (string) config('raiida.workflow_queue', 'revizyseeder-workflows');
    }

    public static function isPaused(): bool
    {
        return (bool) Cache::get(self::PAUSED_CACHE_KEY, false);
    }

    public static function pause(): void
    {
        Cache::forever(self::PAUSED_CACHE_KEY, true);
    }

    public static function resume(): void
    {
        Cache::forget(self::PAUSED_CACHE_KEY);
    }
}

