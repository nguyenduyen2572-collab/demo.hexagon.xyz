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
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', 'different:current_password', Password::min(12)],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'current_password.current_password' => 'Mật khẩu hiện tại không đúng.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.confirmed' => 'Hai lần nhập mật khẩu mới không khớp.',
            'new_password.different' => 'Mật khẩu mới phải khác mật khẩu cũ.',
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['new_password']);
        $user->setRememberToken(Str::random(60));
        $user->save();
        $request->session()->regenerate();

        return redirect()->route('customer.account.index')
            ->with('account_tab', 'security')
            ->with('success', 'Đã cập nhật mật khẩu thành công!');
    }
}
