<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAccountController extends Controller
{
    // Trang hồ sơ: không hiển thị bảng đơn hàng tại đây.
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if((bool) $user->is_admin, 403);

        return view('customer.account', compact('user'));
    }

    // Trang đơn hàng riêng, chỉ lấy đơn liên kết với tài khoản đang đăng nhập.
    public function orders(Request $request)
    {
        $user = $request->user();
        abort_if((bool) $user->is_admin, 403);

        $orders = DB::table('orders')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('customer.orders', compact('user', 'orders'));
    }

    // Chi tiết một đơn của chính tài khoản, không chỉ dựa vào mã đơn.
    public function showOrder(Request $request, string $code)
    {
        $user = $request->user();
        abort_if((bool) $user->is_admin, 403);

        $order = DB::table('orders')
            ->where('user_id', $user->id)
            ->where('order_code', $code)
            ->firstOrFail();

        $items = DB::table('order_items')
            ->where('order_id', $order->id)
            ->orderBy('id')
            ->get();

        return view('customer.order-detail', compact('order', 'items'));
    }
}
