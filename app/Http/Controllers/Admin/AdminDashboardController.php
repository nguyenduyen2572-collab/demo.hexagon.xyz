<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê đơn hàng
        $stats = [
            'total_orders' => DB::table('orders')->count(),

            'pending_orders' => DB::table('orders')
                ->where('status', 'pending_demo')
                ->count(),

            'total_demo_amount' => DB::table('orders')
                ->sum('total_amount'),

            'unique_customers' => DB::table('orders')
                ->distinct()
                ->count('customer_email'),
        ];

        // 2. Lấy 5 đơn hàng mới nhất
        $recentOrders = DB::table('orders')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // 3. Truyền dữ liệu sang giao diện
        return view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
