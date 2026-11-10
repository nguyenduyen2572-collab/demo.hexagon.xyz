<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Separate admin credentials from default web (customer) credentials.
 * Keep the pre-existing EnsureAdmin middleware as defense in depth.
 */
final class EnsureAdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if ($admin !== null) {
            if (! (bool) $admin->is_admin) {
                Auth::guard('admin')->logout();
                abort(403, 'Tài khoản này không có quyền quản trị.');
            }

            // Existing admin controllers, layouts and EnsureAdmin see the admin user.
            Auth::shouldUse('admin');

            return $next($request);
        }

        // Preserve previous behavior: customers receive 403, guests go to login.
        if (Auth::guard('web')->check()) {
            abort(403, 'Tài khoản khách hàng không có quyền quản trị.');
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Bạn cần đăng nhập tài khoản Admin.'], 401);
        }

        return redirect()->route('login');
    }
}
