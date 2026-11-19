<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

/** Danh sách tài khoản đang hoạt động và thực sự có quyền nhận đơn hàng. */
final class HexagonAdminOrderAssignments
{
    public static function canAssign(?User $admin): bool
    {
        return in_array(HexagonAdminRoles::role($admin), ['super_admin', 'manager'], true)
            && HexagonAdminRoles::can($admin, 'orders');
    }

    public static function eligibleAssignees(): Collection
    {
        return User::query()->where('is_admin', true)->where('admin_enabled', true)
            ->orderBy('name')->get(['id', 'name', 'email', 'is_admin', 'admin_role', 'admin_permissions', 'admin_enabled'])
            ->filter(static fn (User $admin): bool => HexagonAdminRoles::role($admin) !== 'super_admin'
                && HexagonAdminRoles::can($admin, 'orders'))
            ->values();
    }
}
