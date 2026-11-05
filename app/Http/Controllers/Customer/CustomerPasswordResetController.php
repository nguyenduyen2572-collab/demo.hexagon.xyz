<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class CustomerPasswordResetController extends Controller
{
    public function showForgot()
    {
        return view('customer.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ]);

        // Chỉ gửi link cho khách hàng. Không phục vụ tài khoản Admin.
        $customer = User::query()
            ->where('email', $validated['email'])
            ->where('is_admin', false)
            ->first();

        if ($customer) {
            Password::sendResetLink(['email' => $customer->email]);
        }

        // Cùng một thông báo dù email có tồn tại hay không.
        return back()->with(
            'status',
            'Nếu email thuộc tài khoản khách hàng HEXAGON, liên kết đặt lại mật khẩu đã được tạo.'
        );
    }

    public function showReset(Request $request, string $token)
    {
        return view('customer.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function reset(Request $request)
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'confirmed', PasswordRule::min(12)],
        ]);

        // Quan trọng: không dùng luồng khách hàng để đặt lại mật khẩu Admin.
        $customer = User::query()
            ->where('email', $validated['email'])
            ->where('is_admin', false)
            ->first();

        if (!$customer) {
            return back()->withErrors([
                'email' => 'Thông tin hoặc liên kết đặt lại mật khẩu không hợp lệ.',
            ])->onlyInput('email');
        }

        $status = Password::reset(
            [
                'email' => $customer->email,
                'token' => $validated['token'],
                'password' => $validated['password'],
                'password_confirmation' => $request->input('password_confirmation'),
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PasswordReset) {
            return redirect()->route('customer.login')
                ->with('status', 'Đặt lại mật khẩu thành công. Hãy đăng nhập bằng mật khẩu mới.');
        }

        return back()->withErrors([
            'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
        ])->onlyInput('email');
    }
}
