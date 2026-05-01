<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RaiidaAdminAuditLog
{
    public function handle(Request $request, Closure $next): Response
    {
        $contextId = $this->resolveContextId($request);
        $request->attributes->set('workflow_context_id', $contextId);

        $user = $request->user();

        $baseContext = [
            'workflow_context_id' => $contextId,
            'method' => $request->method(),
            'path' => '/' . ltrim($request->path(), '/'),
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_role' => $user?->role,
            'ip' => $request->ip(),
        ];

        $startedAt = microtime(true);

        Log::info('raiida.admin_mutation.start', $baseContext);

        try {
            /** @var Response $response */
            $response = $next($request);
        } catch (Throwable $exception) {
            Log::error('raiida.admin_mutation.failed', $baseContext + [
                'duration_ms' => $this->durationMs($startedAt),
                'exception' => $exception::class,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $response->headers->set('X-Workflow-Context-Id', $contextId);

        Log::info('raiida.admin_mutation.completed', $baseContext + [
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $this->durationMs($startedAt),
        ]);

        return $response;
    }

    private function resolveContextId(Request $request): string
    {
        $header = (string) $request->header('X-Workflow-Context-Id', '');
        $header = trim($header);

        return $header !== '' ? $header : (string) Str::uuid();
    }

    private function durationMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }
}
