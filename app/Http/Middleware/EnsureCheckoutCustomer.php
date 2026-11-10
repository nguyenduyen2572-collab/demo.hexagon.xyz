<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only customers may enter checkout. Cart routes deliberately remain public.
 * This middleware replaces the plain 'auth' middleware on the two checkout routes.
 */
class EnsureCheckoutCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Vui lòng đăng nhập để tiếp tục đặt hàng.',
                ], 401);
            }

            // Always intend the GET checkout page, never a POST endpoint.
            // This keeps the cart in the same session during customer login.
            $request->session()->put('url.intended', route('checkout.create'));

            return redirect()
                ->route('customer.login')
                ->with('warning', 'Vui lòng đăng nhập để tiếp tục đặt hàng.');
        }

        if ((bool) $request->user()->is_admin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tài khoản quản trị không thể đặt hàng bằng chức năng dành cho khách hàng.',
                ], 403);
            }

            return redirect()
                ->route('cart.index')
                ->with('error', 'Bạn đang sử dụng tài khoản Admin. Vui lòng đăng xuất và đăng nhập bằng tài khoản khách hàng để đặt hàng.');
        }

        return $next($request);
    }
}
