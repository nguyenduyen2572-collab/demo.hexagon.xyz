<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

/** Quy tắc dùng chung: từ 8 ký tự, chữ hoa + thường + số + ký tự đặc biệt. */
final class HexagonPasswordPolicy
{
    public static function requirements(): array
    {
        return [
            'min:8',
            'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/s',
        ];
    }

    public static function isEnglish(Request $request): bool
    {
        // ui_language: biểu mẫu VI/EN; hx_locale: biểu mẫu quên mật khẩu cũ.
        return $request->input('ui_language', $request->input('hx_locale', 'vi')) === 'en';
    }

    public static function messages(string $field, bool $english): array
    {
        return [
            "$field.required" => $english ? 'Please enter a new password.' : 'Vui lòng nhập mật khẩu mới.',
            "$field.min" => $english ? 'Password must contain at least 8 characters.' : 'Mật khẩu phải có ít nhất 8 ký tự.',
            "$field.regex" => $english
                ? 'Use uppercase, lowercase, a number and a special character.'
                : 'Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt.',
        ];
    }
}
