<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * HEXAGON customer registration: validation and localized field-level errors.
 * Only used by CustomerRegisterController::register; does not alter password reset or login.
 */
final class HexagonRegistrationRules
{
    public static function validate(Request $request): array
    {
        $request->merge([
            'name' => preg_replace('/\s+/u', ' ', trim((string) $request->input('name', ''))),
            'email' => mb_strtolower(trim((string) $request->input('email', ''))),
        ]);

        $en = $request->input('ui_language') === 'en';

        $messages = $en ? [
            'name.required' => 'Please enter your full name.',
            'name.min' => 'The name must contain at least 2 characters.',
            'name.max' => 'The name is too long.',
            'name.regex' => 'Use letters and spaces only; numbers and special characters are not allowed.',
            'email.required' => 'Please enter your email.',
            'email.email' => 'Please enter a valid email address.',
            'email.ends_with' => 'Only @gmail.com addresses are accepted.',
            'email.max' => 'The email address is too long.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'The password must contain at least 8 characters.',
            'password.regex' => 'Use uppercase, lowercase, a number and a special character.',
            'password_confirmation.required' => 'Please confirm your password.',
            'password_confirmation.same' => 'Passwords do not match.',
        ] : [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
            'name.max' => 'Họ và tên quá dài.',
            'name.regex' => 'Họ và tên chỉ được dùng chữ cái và dấu cách, không được nhập số hoặc ký tự đặc biệt.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.ends_with' => 'Chỉ chấp nhận email có đuôi @gmail.com.',
            'email.max' => 'Email quá dài.',
            'email.unique' => 'Email này đã được đăng ký.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.regex' => 'Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt.',
            'password_confirmation.required' => 'Vui lòng nhập lại mật khẩu.',
            'password_confirmation.same' => 'Không trùng với mật khẩu.',
        ];

        // Validate server-side regardless of browser scripting.
        $validator = Validator::make($request->all(), [
            'name' => [
                'bail', 'required', 'string', 'min:2', 'max:150',
                'regex:/^\p{L}[\p{L}\p{M}]*(?: \p{L}[\p{L}\p{M}]*)*$/u',
            ],
            'email' => [
                'bail', 'required', 'string', 'email:rfc', 'max:190',
                'ends_with:@gmail.com', Rule::unique('users', 'email'),
            ],
            'password' => [
                'bail', 'required', 'string', 'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/s',
            ],
            'password_confirmation' => ['bail', 'required', 'same:password'],
        ], $messages);

        return $validator->validate();
    }
}
