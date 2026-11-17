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

            // Only Admin authentication expires after 30 minutes without activity.
            // A separate Customer web session (and the cart) must remain untouched.
            $session = $request->session();
            $previousActivity = $session->get('hexagon_admin_last_activity_at');
            if (is_numeric($previousActivity) && time() - (int) $previousActivity > 30 * 60) {
                return $this->expireAdmin($request, 'Phiên quản trị đã hết hạn do không hoạt động trong 30 phút.');
            }

            // Compare against the current database password. A password update
            // revokes sessions carrying the previous fingerprint on next request.
            // Sessions established before V4 receive a stamp at their first request.
            $currentAdmin = $admin->fresh();
            if ($currentAdmin === null || ! (bool) $currentAdmin->is_admin) {
                return $this->expireAdmin($request, 'Quyền truy cập quản trị đã thay đổi.');
            }
            $fingerprint = hash('sha256', (string) $currentAdmin->getAuthPassword());
            $previousFingerprint = $session->get('hexagon_admin_password_stamp');
            if (is_string($previousFingerprint) && ! hash_equals($fingerprint, $previousFingerprint)) {
                return $this->expireAdmin($request, 'Mật khẩu quản trị đã được thay đổi. Vui lòng đăng nhập lại.');
            }
            $session->put('hexagon_admin_password_stamp', $fingerprint);
            $session->put('hexagon_admin_last_activity_at', time());

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

    private function expireAdmin(Request $request, string $message): Response
    {
        Auth::guard('admin')->logout();
        $request->session()->forget(['hexagon_admin_password_stamp', 'hexagon_admin_last_activity_at']);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401);
        }

        return redirect()->route('login')->with('status', $message);
    }
}
