<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Password maintenance affects the Admin guard only, never Customer auth. */
final class AdminSecurityController extends Controller
{
    public function editPassword(): View
    {
        return view('admin.security.password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => [
                'required', 'string', 'min:12', 'max:128', 'confirmed',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{12,}$/s',
            ],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu Admin phải có tối thiểu 12 ký tự.',
            'new_password.max' => 'Mật khẩu không được vượt quá 128 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'new_password.regex' => 'Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt.',
        ]);

        $admin = Auth::guard('admin')->user();
        abort_unless($admin && (bool) $admin->is_admin, 403);

        if (! Hash::check($data['current_password'], (string) $admin->getAuthPassword())) {
            throw ValidationException::withMessages([
                'current_password' => 'Mật khẩu hiện tại không chính xác.',
            ]);
        }
        if (Hash::check($data['new_password'], (string) $admin->getAuthPassword())) {
            throw ValidationException::withMessages([
                'new_password' => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
            ]);
        }

        // User has the standard Laravel hashed password cast.
        // Rotating remember_token invalidates existing "remember me" tokens.
        $admin->forceFill([
            'password' => $data['new_password'],
            'remember_token' => Str::random(60),
        ])->save();

        // Require fresh Admin login. Preserve a possible Customer web session.
        Auth::guard('admin')->logout();
        $request->session()->forget(['hexagon_admin_password_stamp', 'hexagon_admin_last_activity_at']);
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Đổi mật khẩu quản trị thành công. Vui lòng đăng nhập lại.');
    }
}
