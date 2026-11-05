<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;

class CustomerRegisterController extends Controller
{
    // Hiển thị trang đăng ký
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect(
                Auth::user()->is_admin
                    ? route('admin.dashboard')
                    : '/'
            );
        }

        return view('customer.register');
    }

    // Xử lý đăng ký tài khoản
    public function register(Request $request)
    {
        // Không tạo tài khoản mới khi đang đăng nhập
        if (Auth::check()) {
            return redirect(
                Auth::user()->is_admin
                    ? route('admin.dashboard')
                    : '/'
            );
        }

        // Chuẩn hóa email
        $request->merge([
            'email' => strtolower(
                trim((string) $request->input('email', ''))
            ),
        ]);

        // Kiểm tra thông tin
        $validated = \App\Support\HexagonRegistrationRules::validate($request);

        // Tạo tài khoản khách hàng
        $user = new User();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make(
            $validated['password']
        );

        // Luôn tạo tài khoản thường
        $user->is_admin = false;

        $user->save();
         event(new Registered($user));

        return redirect()
            ->route('customer.register')
            ->with(
                'success',
                'Đăng ký tài khoản thành công! Vui lòng xác minh email.'
            );
    }
}
