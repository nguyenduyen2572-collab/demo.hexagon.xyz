<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminOrderController extends Controller
{
    // Các trạng thái sử dụng trong bản Demo
    private const STATUS_LABELS = [
        'pending_demo' => 'Chờ xử lý',
        'processing_demo' => 'Đang xử lý',
        'completed_demo' => 'Đã xử lý',
        'cancelled_demo' => 'Đã hủy',
    ];

    // Quy trình chuyển trạng thái hợp lệ
    private const NEXT_STATUSES = [
        'pending_demo' => [
            'processing_demo',
            'cancelled_demo',
        ],
        'processing_demo' => [
            'completed_demo',
            'cancelled_demo',
        ],
        'completed_demo' => [],
        'cancelled_demo' => [],
    ];

    // Danh sách đơn hàng
    public function index()
    {
        $orders = \App\Support\HexagonAdminOrderFilters::apply(DB::table('orders'), request())
            ->orderByDesc('id')
            ->paginate(10)->withQueryString();

        return view('admin.orders', [
            'orders' => $orders,
        ]);
    }

    // Xem chi tiết đơn hàng
    public function show(int $id)
    {
        $order = DB::table('orders')
            ->where('id', $id)
            ->first();

        abort_if(!$order, 404);

        $items = DB::table('order_items')
            ->where('order_id', $order->id)
            ->orderBy('id')
            ->get();

        return view('admin.order-detail', [
            'order' => $order,
            'items' => $items,
            'statusLabels' => self::STATUS_LABELS,
            'nextStatuses' =>
                self::NEXT_STATUSES[$order->status] ?? [],
        ]);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(array_keys(self::STATUS_LABELS)),
            ],
        ]);

        $order = DB::table('orders')
            ->where('id', $id)
            ->first();

        abort_if(!$order, 404);

        $nextStatuses =
            self::NEXT_STATUSES[$order->status] ?? [];

        if (!in_array(
            $validated['status'],
            $nextStatuses,
            true
        )) {
            return back()->withErrors([
                'status' => 'Không thể chuyển sang trạng thái này.',
            ]);
        }

        // Chỉ cập nhật nếu trạng thái chưa bị thay đổi
        $updated = DB::table('orders')
            ->where('id', $id)
            ->where('status', $order->status)
            ->update([
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

        if ($updated !== 1) {
            return back()->withErrors([
                'status' => 'Đơn hàng đã được cập nhật ở nơi khác. Hãy tải lại trang.',
            ]);
        }

        return redirect()
            ->route('admin.orders.show', ['id' => $id])
            ->with('success', 'Cập nhật trạng thái thành công!');
    }
}
