<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\HexagonAdminRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Các thao tác chỉ chạy sau middleware Super Admin. */
final class AdminTeamController extends Controller
{
    public function index(): View
    {
        return view('admin.team.index', [
            'users' => User::query()->where('is_admin', true)->orderBy('id')->paginate(15),
            'capabilities' => HexagonAdminRoles::CAPABILITIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:12', 'max:128',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{12,}$/s', 'confirmed'],
            'role' => ['required', Rule::in(['manager', 'staff'])],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(HexagonAdminRoles::CAPABILITIES))],
        ]);
        $permissions = $this->permissionsForRole($data['role'], $data['permissions'] ?? []);
        $employee = new User();
        $employee->forceFill([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'is_admin' => true,
            'admin_role' => $data['role'],
            'admin_permissions' => $permissions,
            'admin_enabled' => true,
        ])->save();
        return redirect()->route('admin.team.index')->with('success', 'Đã tạo tài khoản nhân sự. Hãy chuyển mật khẩu cho nhân viên qua kênh riêng.');
    }

    public function edit(int $id): View
    {
        $employee = User::query()->where('is_admin', true)->findOrFail($id);
        abort_if(HexagonAdminRoles::role($employee) === 'super_admin', 403);
        return view('admin.team.edit', [
            'employee' => $employee,
            'capabilities' => HexagonAdminRoles::CAPABILITIES,
            'granted' => HexagonAdminRoles::capabilities($employee),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'role' => ['required', Rule::in(['manager', 'staff'])],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(HexagonAdminRoles::CAPABILITIES))],
            'admin_enabled' => ['required', 'boolean'],
        ]);
        $employee = User::query()->where('is_admin', true)->findOrFail($id);
        abort_if(HexagonAdminRoles::role($employee) === 'super_admin', 403);
        abort_if($employee->id === $request->user('admin')?->id, 403);
        $employee->forceFill([
            'name' => trim($data['name']),
            'admin_role' => $data['role'],
            'admin_permissions' => $this->permissionsForRole($data['role'], $data['permissions'] ?? []),
            'admin_enabled' => (bool) $data['admin_enabled'],
        ])->save();
        return redirect()->route('admin.team.index')->with('success', 'Đã cập nhật phân quyền nhân viên.');
    }

    private function permissionsForRole(string $role, array $requested): array
    {
        $allowed = $role === 'staff' ? ['saas', 'inquiries'] : array_keys(HexagonAdminRoles::CAPABILITIES);
        // Không cấp quyền ngoài phạm vi, kể cả khi gửi POST thủ công.
        if (array_diff($requested, $allowed) !== []) {
            throw ValidationException::withMessages([
                'permissions' => 'Nhân viên chỉ được cấp quyền Dịch vụ SaaS hoặc Yêu cầu tư vấn SaaS.',
            ]);
        }
        return array_values(array_unique($requested));
    }
}
