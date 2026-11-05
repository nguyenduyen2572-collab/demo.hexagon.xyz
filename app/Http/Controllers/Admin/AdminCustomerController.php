<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCustomerController extends Controller
{
    // Danh sách khách đã từng đặt hàng
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $search = trim($validated['q'] ?? '');

        // Nhóm các đơn theo email khách hàng
        $summary = DB::table('orders')
            ->selectRaw('LOWER(TRIM(customer_email)) AS email_key')
            ->selectRaw('MAX(id) AS latest_order_id')
            ->selectRaw('COUNT(*) AS order_count')
            ->selectRaw('SUM(total_amount) AS total_value')
            ->selectRaw('MAX(created_at) AS last_order_at')
            ->groupByRaw('LOWER(TRIM(customer_email))');

        // Lấy thông tin liên hệ từ đơn mới nhất
        $query = DB::query()
            ->fromSub($summary, 'summary')
            ->join(
                'orders AS latest_order',
                'latest_order.id',
                '=',
                'summary.latest_order_id'
            )
            ->select([
                'summary.latest_order_id',
                'summary.order_count',
                'summary.total_value',
                'summary.last_order_at',
                'latest_order.customer_name',
                'latest_order.customer_email',
                'latest_order.customer_phone',
            ]);

        // Tìm theo thông tin liên hệ mới nhất
        if ($search !== '') {
            $keyword = '%' . $search . '%';

            $query->where(function ($q) use ($keyword) {
                $q->where(
                    'latest_order.customer_name',
                    'like',
                    $keyword
                )
                ->orWhere(
                    'latest_order.customer_email',
                    'like',
                    $keyword
                )
                ->orWhere(
                    'latest_order.customer_phone',
                    'like',
                    $keyword
                );
            });
        }

        $customers = $query
            ->orderByDesc('summary.latest_order_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    // Xem lịch sử một khách hàng
    public function show(int $id)
    {
        // ID này là ID một đơn hàng đại diện
        $anchor = DB::table('orders')
            ->where('id', $id)
            ->first();

        abort_if(!$anchor, 404);

        $emailKey = strtolower(
            trim($anchor->customer_email)
        );

        // Toàn bộ đơn của cùng email
        $customerOrders = DB::table('orders')
            ->whereRaw(
                'LOWER(TRIM(customer_email)) = ?',
                [$emailKey]
            );

        $customer = (clone $customerOrders)
            ->orderByDesc('id')
            ->first();

        $stats = (clone $customerOrders)
            ->selectRaw('COUNT(*) AS order_count')
            ->selectRaw('COALESCE(SUM(total_amount), 0) AS total_value')
            ->selectRaw('MAX(created_at) AS last_order_at')
            ->first();

        $orders = $customerOrders
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.customer-detail', [
            'customer' => $customer,
            'stats' => $stats,
            'orders' => $orders,
        ]);
    }
}
