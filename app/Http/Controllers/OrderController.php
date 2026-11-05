<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // Hiển thị trang đặt hàng
    public function create(Request $request)
    {
        $cart = $request->session()->get('hexagon_cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng đang trống.');
        }

        return view('checkout', [
            'items' => array_values($cart),
            'total' => array_sum(
                array_column($cart, 'registration_price')
            ),
        ]);
    }

    // Xử lý và lưu đơn hàng vào MySQL
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_email' => 'required|email|max:190',
            'customer_phone' => 'required|string|max:30',
        ]);

        $cart = $request->session()->get('hexagon_cart', []);

        if (!is_array($cart) || empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng đang trống.');
        }

        // Lấy bảng giá hiện tại từ MySQL
        $prices = DB::table('domain_prices')
            ->orderByRaw('CHAR_LENGTH(extension) DESC')
            ->get();

        $items = [];
        $total = 0;

        foreach ($cart as $cartItem) {
            if (!is_array($cartItem)) {
                return back()->with(
                    'error',
                    'Dữ liệu giỏ hàng không hợp lệ.'
                );
            }

            $domain = strtolower(
                trim((string) ($cartItem['domain'] ?? ''))
            );

            if (
                strlen($domain) > 190 ||
                !filter_var(
                    $domain,
                    FILTER_VALIDATE_DOMAIN,
                    FILTER_FLAG_HOSTNAME
                )
            ) {
                return back()->with(
                    'error',
                    'Tên miền trong giỏ hàng không hợp lệ.'
                );
            }

            // Kiểm tra lại trạng thái tên miền
            $status = DB::table('mock_domain_statuses')
                ->where('domain', $domain)
                ->first();

            if (!$status || !(bool) $status->is_available) {
                return redirect()->route('cart.index')
                    ->with(
                        'error',
                        "Tên miền {$domain} không khả dụng trong dữ liệu demo."
                    );
            }

            // Xác định giá theo đuôi dài nhất
            $price = $prices->first(
                fn ($row) => str_ends_with(
                    $domain,
                    $row->extension
                )
            );

            if (!$price) {
                return back()->with(
                    'error',
                    "Chưa có bảng giá cho {$domain}."
                );
            }
            if (!(bool) $price->is_active) {
    return redirect()
        ->route('cart.index')
        ->with(
            'error',
            "Tên miền {$domain} đang tạm ngừng kinh doanh."
        );
}

            $amount = (int) $price->registration_price;

            $items[] = [
                'domain_name' => $domain,
                'extension' => $price->extension,
                'registration_price' => $amount,
                'registration_years' => 1,
                'line_total' => $amount,
            ];

            $total += $amount;
        }

        $orderCode = (string) Str::uuid();
        
        // Lấy ID khách hàng đang đăng nhập
        $loggedUser = $request->user();

        $userId = (
            $loggedUser && !$loggedUser->is_admin
        ) ? $loggedUser->id : null;


        // Chỉ hoàn tất khi cả đơn hàng và chi tiết đều lưu được
        DB::transaction(function () use (
            $validated,
            $items,
            $total,
            $orderCode,
            $userId
        ) {
            $now = now();

            $orderId = DB::table('orders')->insertGetId([
                'order_code' => $orderCode,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'total_amount' => $total,
                'status' => 'pending_demo',
                'is_mock' => true,
                'created_at' => $now,
                'updated_at' => $now,
                'user_id' => $userId,
            ]);

            foreach ($items as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'domain_name' => $item['domain_name'],
                    'extension' => $item['extension'],
                    'registration_price' =>
                        $item['registration_price'],
                    'registration_years' =>
                        $item['registration_years'],
                    'line_total' => $item['line_total'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        // Chỉ cho phiên làm việc tạo đơn xem trang xác nhận
        
        // Lấy danh sách mã đơn hàng trong Session
        $orderCodes = $request->session()->get(
            'hexagon_order_codes',
            []
        );

        // Đảm bảo dữ liệu là một mảng
        if (!is_array($orderCodes)) {
            $orderCodes = [];
        }

        // Thêm mã đơn hàng mới, tránh trùng lặp
        if (!in_array($orderCode, $orderCodes, true)) {
            $orderCodes[] = $orderCode;
        }

        // Lưu lại danh sách vào Session
        $request->session()->put(
            'hexagon_order_codes',
            $orderCodes
        );


        // Xóa giỏ hàng sau khi lưu đơn thành công
        $request->session()->forget('hexagon_cart');

        return redirect()->route('orders.show', $orderCode);
    }

    // Hiển thị đơn hàng vừa tạo
    public function show(Request $request, string $code)
    {
        $allowedCodes = $request->session()->get(
            'hexagon_order_codes',
            []
        );

        abort_unless(
            is_array($allowedCodes) &&
            in_array($code, $allowedCodes, true),
            403
        );

        $order = DB::table('orders')
            ->where('order_code', $code)
            ->firstOrFail();

        $items = DB::table('order_items')
            ->where('order_id', $order->id)
            ->get();

        return view('order-success', compact('order', 'items'));
    }
}
