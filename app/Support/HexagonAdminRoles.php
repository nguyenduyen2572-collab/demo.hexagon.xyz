<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;

/** Quyền của Admin; quyền truy cập luôn được xác minh trên máy chủ. */
final class HexagonAdminRoles
{
    public const CAPABILITIES = [
        'orders' => 'Đơn hàng',
        'pricing' => 'Bảng giá tên miền',
        'customers' => 'Khách hàng',
        'saas' => 'Dịch vụ SaaS',
        'inquiries' => 'Yêu cầu tư vấn SaaS',
    ];

    public static function role(?User $user): string
    {
        if ($user === null || ! (bool) $user->is_admin) return 'customer';
        if (in_array($user->admin_role, ['super_admin', 'manager', 'staff'], true)) {
            return (string) $user->admin_role;
        }
        // Tương thích Admin cũ trước V9: không làm mất quyền khi chưa bootstrap.
        return 'super_admin';
    }

    public static function capabilities(?User $user): array
    {
        if ($user === null || ! (bool) $user->is_admin || $user->admin_enabled === false) return [];
        if (self::role($user) === 'super_admin') return array_keys(self::CAPABILITIES);
        $raw = $user->admin_permissions;
        $list = is_array($raw) ? $raw : json_decode((string) ($raw ?: '[]'), true);
        if (! is_array($list)) return [];
        return array_values(array_intersect(array_keys(self::CAPABILITIES), $list));
    }

    public static function can(?User $user, string $capability): bool
    {
        if ($user === null || ! (bool) $user->is_admin || $user->admin_enabled === false) return false;
        if (self::role($user) === 'super_admin') return true;
        if (! in_array($capability, self::capabilities($user), true)) return false;
        // Nhân viên tư vấn được vào danh sách yêu cầu, nhưng Controller
        // chỉ trả dữ liệu đã phân công cho chính họ (V9.1).
        return self::role($user) === 'manager' || in_array($capability, ['saas', 'inquiries'], true);
    }

    public static function displayRole(?User $user): string
    {
        return match (self::role($user)) {
            'super_admin' => 'Super Admin',
            'manager' => 'Quản lý bộ phận',
            'staff' => 'Nhân viên',
            default => 'Khách hàng',
        };
    }
}
