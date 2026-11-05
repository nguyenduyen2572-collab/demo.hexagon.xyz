<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class CustomerEmailVerificationController extends Controller
{
    public function notice(Request $request)
    {
        $user = $request->user();
        abort_if((bool) $user->is_admin, 403);

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('customer.account.index');
        }

        return view('customer.verify-email', ['user' => $user]);
    }

    public function verify(EmailVerificationRequest $request)
    {
        abort_if((bool) $request->user()->is_admin, 403);
        $request->fulfill();

        return redirect()->route('customer.account.index')
            ->with('success', 'Email của bạn đã được xác minh thành công.');
    }

    public function resend(Request $request)
    {
        $user = $request->user();
        abort_if((bool) $user->is_admin, 403);

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('customer.account.index');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'Đã tạo lại liên kết xác minh. Hãy kiểm tra log email trên Local.');
    }
}
