<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Giới hạn riêng cho tính năng gợi ý GoDaddy.
 * Không được chia chung bucket với nút Kiểm tra tên miền.
 */
final class HexagonDomainIdeasThrottle
{
    public function handle(Request $request, Closure $next): Response
    {
        $fingerprint = hash('sha256', (string) $request->ip() . '|' . (string) $request->userAgent());
        $key = 'hx-domain-ideas-v1:' . $fingerprint;

        if (RateLimiter::increment($key, 1) > 1) {
            $seconds = max(1, RateLimiter::availableIn($key));
            return response()->json([
                'success' => false,
                'message' => 'Đang có nhiều yêu cầu gợi ý. Vui lòng thử lại sau ít giây.',
                'retry_after' => $seconds,
                'items' => [],
            ], 429, [
                'Retry-After' => (string) $seconds,
                'Cache-Control' => 'no-store',
            ]);
        }

        return $next($request);
    }
}
