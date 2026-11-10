<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CustomerSettingsController extends Controller
{
    private function ensureCustomer(Request $request): void
    {
        abort_if(!$request->user() || (bool) $request->user()->is_admin, 403);
    }

    // Giữ URL cũ hoạt động, nhưng chỉ dùng một trang tài khoản thống nhất.
    public function editProfile(Request $request)
    {
        $this->ensureCustomer($request);
        return redirect()->route('customer.account.index', ['tab' => 'profile']);
    }

    public function editPassword(Request $request)
    {
        $this->ensureCustomer($request);
        return redirect()->route('customer.account.index', ['tab' => 'security']);
    }

    public function updateProfile(Request $request)
    {
        $this->ensureCustomer($request);
        $request->merge(['name' => trim((string) $request->input('name', ''))]);
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
            'name.max' => 'Họ và tên không được quá 150 ký tự.',
        ]);

        $user = $request->user();
        $user->name = $validated['name'];
        $user->save();

        return redirect()->route('customer.account.index')
            ->with('account_tab', 'profile')
            ->with('success', 'Đã cập nhật hồ sơ thành công!');
    }

    public function updatePassword(Request $request)
    {
        $this->ensureCustomer($request);
        // HEXAGON_ACCOUNT_SECURITY_V2: dùng một quy tắc mật khẩu cho toàn bộ luồng khách hàng.
        $english = \App\Support\HexagonPasswordPolicy::isEnglish($request);
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'different:current_password', ...\App\Support\HexagonPasswordPolicy::requirements()],
            'new_password_confirmation' => ['required', 'same:new_password'],
        ], array_merge([
            'current_password.required' => $english ? 'Please enter your current password.' : 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => $english ? 'Your current password is incorrect.' : 'Mật khẩu hiện tại không đúng.',
            'new_password.different' => $english ? 'New password must be different from the current password.' : 'Mật khẩu mới phải khác mật khẩu cũ.',
            'new_password_confirmation.required' => $english ? 'Please confirm your new password.' : 'Vui lòng nhập lại mật khẩu mới.',
            'new_password_confirmation.same' => $english ? 'Passwords do not match.' : 'Không trùng với mật khẩu.',
        ], \App\Support\HexagonPasswordPolicy::messages('new_password', $english)));

        $user = $request->user();
        $user->password = Hash::make($validated['new_password']);
        $user->setRememberToken(Str::random(60));
        $user->save();
        $request->session()->regenerate();

        return redirect()->route('customer.account.index')
            ->with('account_tab', 'security')
            ->with('success', $english ? 'Password updated successfully!' : 'Đã cập nhật mật khẩu thành công!');
    }
}
