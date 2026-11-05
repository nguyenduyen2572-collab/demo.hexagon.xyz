<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Hiển thị giỏ hàng
    public function index(Request $request)
    {
        $cart = $request->session()->get('hexagon_cart', []);

        return view('cart', [
            'items' => array_values($cart),
            'total' => array_sum(
                array_column($cart, 'registration_price')
            ),
        ]);
    }

    // Thêm tên miền vào giỏ hàng
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:190'],
        ]);

        $domain = strtolower(trim($validated['domain']));

        if (!filter_var(
            $domain,
            FILTER_VALIDATE_DOMAIN,
            FILTER_FLAG_HOSTNAME
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Tên miền không hợp lệ',
            ], 422);
        }

        // Kiểm tra lại trạng thái tại Backend
        $status = DB::table('mock_domain_statuses')
            ->where('domain', $domain)
            ->first();

        if (!$status || !$status->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'Tên miền không khả dụng trong dữ liệu demo',
            ], 422);
        }

        // Lấy giá trực tiếp từ MySQL
        $price = DB::table('domain_prices')
            ->orderByRaw('CHAR_LENGTH(extension) DESC')
            ->get()
            ->first(fn ($item) =>
                str_ends_with($domain, $item->extension)
            );

        if (!$price) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa hỗ trợ đuôi tên miền này',
            ], 422);
        }
        if (!(bool) $price->is_active) {
        return response()->json([
            'success' => false,
            'message' => 'Đuôi tên miền này đã tạm ngừng kinh doanh.',
        ], 422);
    }

        $cart = $request->session()->get('hexagon_cart', []);

        // Dùng tên miền làm khóa để tránh thêm trùng
        $cart[$domain] = [
            'domain' => $domain,
            'extension' => $price->extension,
            'registration_price' =>
                (int) $price->registration_price,
        ];

        $request->session()->put('hexagon_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => count($cart),
        ]);
    }

    // Xóa tên miền khỏi giỏ hàng
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:190'],
        ]);

        $domain = strtolower(trim($validated['domain']));

        $cart = $request->session()->get('hexagon_cart', []);

        unset($cart[$domain]);

        $request->session()->put('hexagon_cart', $cart);

        return redirect()->route('cart.index');
    }
}
