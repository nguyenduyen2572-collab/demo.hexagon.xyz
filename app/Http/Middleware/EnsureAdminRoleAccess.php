<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\HexagonAdminRoles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Chặn ngay tại backend: ẩn menu không thay thế được kiểm tra quyền. */
final class EnsureAdminRoleAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();
        abort_unless($admin !== null && (bool) $admin->is_admin, 403);

        if ($admin->admin_enabled === false) {
            Auth::guard('admin')->logout();
            $request->session()->forget(['hexagon_admin_password_stamp', 'hexagon_admin_last_activity_at']);
            $request->session()->regenerate();
            return redirect()->route('login')->with('status', 'Tài khoản quản trị đã bị tạm khóa.');
        }

        $role = HexagonAdminRoles::role($admin);
        if ($request->routeIs('admin.logout', 'admin.security.*')) return $next($request);

        if ($request->routeIs('admin.dashboard')) {
            if ($role === 'super_admin') return $next($request);
            // Không công khai thống kê toàn công ty cho nhân viên/bộ phận.
            return response()->view('admin.team.home', [
                'capabilities' => HexagonAdminRoles::capabilities($admin),
            ]);
        }
        if ($request->routeIs('admin.team.*')) {
            abort_unless($role === 'super_admin', 403);
            return $next($request);
        }
        $scope = match (true) {
            $request->routeIs('admin.orders.*') => 'orders',
            $request->routeIs('admin.domain-prices.*') => 'pricing',
            $request->routeIs('admin.customers.*') => 'customers',
            $request->routeIs('admin.saas.*') => 'saas',
            $request->routeIs('admin.saas-inquiries.*') => 'inquiries',
            default => null,
        };
        // Không mở quyền cho route Admin mới nếu chưa được gán phạm vi rõ ràng.
        abort_unless($scope !== null && HexagonAdminRoles::can($admin, $scope), 403);
        return $next($request);
    }
}
