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

        // A session created BEFORE Admin and Customer guards were separated
        // may still contain an Admin account under the old "web" guard.
        // This is a migration case, not a Customer attempting to gain access.
        $webUser = Auth::guard('web')->user();

        if ($webUser !== null && (bool) $webUser->is_admin) {
            if ($request->routeIs('admin.logout')) {
                // The legacy Admin logout button must work without changing
                // a separate Customer session or invalidating the whole session.
                Auth::guard('web')->logout();
                $request->session()->regenerate();

                return redirect()->route('login')
                    ->with('status', 'Đã đăng xuất phiên quản trị cũ.');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Phiên Admin cũ đã thay đổi. Vui lòng đăng nhập lại.',
                ], 401);
            }

            return redirect()->route('login')->with(
                'status',
                'Phiên Admin cũ cần đăng nhập lại để chuyển sang cơ chế xác thực mới.'
            );
        }

        // A real Customer must never gain access to Admin routes.
        if ($webUser !== null) {
            abort(403, 'Tài khoản khách hàng không có quyền quản trị.');
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Bạn cần đăng nhập tài khoản Admin.'], 401);
        }

        return redirect()->route('login');
    }
}
