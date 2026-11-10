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

    // HEXAGON_FORGOT_EMAILCHECK_V1: only registered non-admin customers may request a reset link.
    public function sendLink(Request $request)
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email', ''))),
        ]);

        $language = $request->input('hx_locale') === 'en' ? 'en' : 'vi';
        $english = $language === 'en';

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ], [
            'email.required' => $english ? 'Please enter your email address.' : 'Vui lòng nhập địa chỉ email.',
            'email.email' => $english ? 'Please enter a valid email address.' : 'Địa chỉ email không đúng định dạng.',
            'email.max' => $english ? 'Email must not exceed 190 characters.' : 'Email không được vượt quá 190 ký tự.',
        ]);

        // Do not allow the customer flow to send password links to Admin accounts.
        $customer = User::query()
            ->where('email', $validated['email'])
            ->where('is_admin', false)
            ->first();

        if ($customer === null) {
            return back()->withErrors([
                'email' => $english
                    ? 'This email address is not registered in HEXAGON.'
                    : 'Email này chưa được đăng ký trong hệ thống HEXAGON.',
            ])->onlyInput('email');
        }

        $status = Password::sendResetLink(['email' => $customer->email]);

        if ($status === Password::ResetLinkSent) {
            return back()->with('status', $english
                ? 'A password reset link has been sent. Please check your email.'
                : 'Đã tạo liên kết đặt lại mật khẩu. Vui lòng kiểm tra email của bạn.');
        }

        if ($status === Password::ResetThrottled) {
            return back()->withErrors([
                'email' => $english
                    ? 'Too many requests. Please try again in a moment.'
                    : 'Bạn đã yêu cầu quá nhiều lần. Vui lòng thử lại sau.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => $english
                ? 'Unable to send the reset link right now. Please try again.'
                : 'Chưa thể gửi liên kết lúc này. Vui lòng thử lại sau.',
        ])->onlyInput('email');
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

        // HEXAGON_RESET_PASSWORD_SECURITY_V2: chỉ cập nhật chính sách, giữ nguyên xác thực token.
        $english = \App\Support\HexagonPasswordPolicy::isEnglish($request);
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', ...\App\Support\HexagonPasswordPolicy::requirements()],
            'password_confirmation' => ['required', 'same:password'],
        ], array_merge([
            'token.required' => $english ? 'Invalid or missing reset link.' : 'Liên kết đặt lại mật khẩu không hợp lệ.',
            'email.required' => $english ? 'Please enter your email address.' : 'Vui lòng nhập địa chỉ email.',
            'email.email' => $english ? 'Please enter a valid email address.' : 'Email không đúng định dạng.',
            'password_confirmation.required' => $english ? 'Please confirm your new password.' : 'Vui lòng nhập lại mật khẩu mới.',
            'password_confirmation.same' => $english ? 'Passwords do not match.' : 'Không trùng với mật khẩu.',
        ], \App\Support\HexagonPasswordPolicy::messages('password', $english)));

        // Quan trọng: không dùng luồng khách hàng để đặt lại mật khẩu Admin.
        $customer = User::query()
            ->where('email', $validated['email'])
            ->where('is_admin', false)
            ->first();

        if (!$customer) {
            return back()->withErrors([
                'email' => $english ? 'Invalid account or password reset link.' : 'Thông tin hoặc liên kết đặt lại mật khẩu không hợp lệ.',
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
                ->with('status', $english ? 'Password reset successfully. Please sign in.' : 'Đặt lại mật khẩu thành công. Hãy đăng nhập bằng mật khẩu mới.');
        }

        return back()->withErrors([
            'email' => $english ? 'The password reset link is invalid or expired.' : 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
        ])->onlyInput('email');
    }
}
