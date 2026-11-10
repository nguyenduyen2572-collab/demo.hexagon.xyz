<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Limit domain availability lookups to one per three seconds per client fingerprint.
 * Typing suggestions never call this endpoint.
 */
final class HexagonDomainSearchCooldown
{
    public function handle(Request $request, Closure $next): Response
    {
        // IP + user agent scopes clients without requiring sessions for routes/api.php.
        // Keep full identifying details out of cache keys.
        $fingerprint = hash('sha256', (string) $request->ip() . '|' . (string) $request->userAgent());
        $key = 'hx-domain-lookup-v1:' . $fingerprint;

        // increment returns the hit count, avoiding a check-then-hit race.
        $attempts = RateLimiter::increment($key, 3);

        if ($attempts > 1) {
            $seconds = max(1, RateLimiter::availableIn($key));

            return response()->json([
                'success' => false,
                'message' => 'Bạn đang kiểm tra quá nhanh. Vui lòng thử lại sau ít giây.',
                'retry_after' => $seconds,
            ], 429, [
                'Retry-After' => (string) $seconds,
                'Cache-Control' => 'no-store',
            ]);
        }

        return $next($request);
    }
}
