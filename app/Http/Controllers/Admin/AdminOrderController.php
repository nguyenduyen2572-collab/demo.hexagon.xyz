<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\HexagonAdminOrderAssignments;
use App\Support\HexagonAdminOrderNavigation;
use App\Support\HexagonAdminRoles;
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

    // Danh sách đơn hàng: nhân viên chỉ được xem đơn được giao.
    public function index(Request $request)
    {
        $admin = $request->user('admin');
        $isStaff = HexagonAdminRoles::role($admin) === 'staff';
        $query = DB::table('orders');
        if ($isStaff) {
            $query->where('assigned_admin_id', $admin->id);
        } else {
            $assigned = $request->query('assigned');
            if ($assigned === 'mine') {
                $query->where('assigned_admin_id', $admin->id);
            } elseif ($assigned === 'unassigned') {
                $query->whereNull('assigned_admin_id');
            } elseif (is_string($assigned) && preg_match('/^[1-9][0-9]{0,8}$/D', $assigned)) {
                $query->where('assigned_admin_id', (int) $assigned);
            }
        }
        $orders = \App\Support\HexagonAdminOrderFilters::apply($query, $request)
            ->orderByDesc('id')->paginate(10)->withQueryString();

        $names = User::query()->whereIn('id', $orders->getCollection()
            ->pluck('assigned_admin_id')->filter()->unique()->all())
            ->pluck('name', 'id');
        return view('admin.orders', [
            'orders' => $orders,
            'isStaff' => $isStaff,
            'assignees' => $isStaff ? collect() : HexagonAdminOrderAssignments::eligibleAssignees(),
            'assignedNames' => $names,
        ]);
    }

    public function show(Request $request, int $id)
    {
        $admin = $request->user('admin');
        $isStaff = HexagonAdminRoles::role($admin) === 'staff';
        $query = DB::table('orders')->where('id', $id);
        if ($isStaff) {
            $query->where('assigned_admin_id', $admin->id);
        }
        $order = $query->first();
        abort_if($order === null, 404);

        $items = DB::table('order_items')->where('order_id', $id)->orderBy('id')->get();
        $history = DB::table('order_assignment_histories as h')
            ->leftJoin('users as actor', 'actor.id', '=', 'h.changed_by')
            ->leftJoin('users as previous', 'previous.id', '=', 'h.from_admin_id')
            ->leftJoin('users as next_admin', 'next_admin.id', '=', 'h.to_admin_id')
            ->where('h.order_id', $id)
            ->select('h.*', 'actor.name as actor_name',
                'previous.name as previous_name', 'next_admin.name as next_name')
            ->orderByDesc('h.id')->limit(10)->get();
        return view('admin.order-detail', [
            'order' => $order,
            'items' => $items,
            'statusLabels' => self::STATUS_LABELS,
            'nextStatuses' => self::NEXT_STATUSES[$order->status] ?? [],
            'isStaff' => $isStaff,
            'canAssign' => HexagonAdminOrderAssignments::canAssign($admin),
            'assignees' => $isStaff ? collect() : HexagonAdminOrderAssignments::eligibleAssignees(),
            'assignedName' => $order->assigned_admin_id
                ? User::query()->whereKey($order->assigned_admin_id)->value('name') : null,
            'assignmentHistory' => $history,
        ]);
    }

    // Phân công hoặc chuyển người phụ trách, chỉ Super Admin và quản lý đơn hàng.
    public function assign(Request $request, int $id)
    {
        $admin = $request->user('admin');
        abort_unless(HexagonAdminOrderAssignments::canAssign($admin), 403);

        $ids = HexagonAdminOrderAssignments::eligibleAssignees()->pluck('id')->all();
        $data = $request->validate([
            'assigned_admin_id' => ['present', 'nullable', 'integer', Rule::in($ids)],
        ], [
            'assigned_admin_id.in' => 'Chỉ được giao đơn cho tài khoản Admin đang hoạt động và có quyền đơn hàng.',
        ]);
        $target = isset($data['assigned_admin_id']) ? (int) $data['assigned_admin_id'] : null;

        $changed = DB::transaction(function () use ($id, $admin, $target): bool {
            $order = DB::table('orders')->where('id', $id)->lockForUpdate()->first();
            abort_if($order === null, 404);
            if (in_array($order->status, ['completed_demo', 'cancelled_demo'], true)) {
                return false;
            }
            $previous = $order->assigned_admin_id === null ? null : (int) $order->assigned_admin_id;
            if ($previous === $target) return false;
            // Kiểm lại sau khi khóa đơn, tránh nhận phân công cho tài khoản vừa bị vô hiệu hóa.
            if ($target !== null && ! HexagonAdminOrderAssignments::eligibleAssignees()->contains('id', $target)) {
                abort(422, 'Nhân viên đã bị khóa hoặc không còn quyền nhận đơn.');
            }
            DB::table('orders')->where('id', $id)->update([
                'assigned_admin_id' => $target, 'updated_at' => now(),
            ]);
            DB::table('order_assignment_histories')->insert([
                'order_id' => $id,
                'changed_by' => $admin->id,
                'from_admin_id' => $previous,
                'to_admin_id' => $target,
                'created_at' => now(),
            ]);
            return true;
        }, 3);
        return redirect()->route('admin.orders.show', array_merge(
            ['id' => $id], HexagonAdminOrderNavigation::filters($request)
        ))->with($changed ? 'success' : 'info', $changed
            ? 'Đã cập nhật người phụ trách đơn hàng.'
            : 'Không có thay đổi phân công, hoặc đơn hàng đã kết thúc.');
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, int $id)
    {
        $admin = $request->user('admin');
        if (HexagonAdminRoles::role($admin) === 'staff') {
            abort_unless(DB::table('orders')->where('id', $id)
                ->where('assigned_admin_id', $admin->id)->exists(), 404);
        }
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
            ->route('admin.orders.show', array_merge(
                ['id' => $id],
                \App\Support\HexagonAdminOrderNavigation::filters($request)
            ))
            ->with('success', 'Cập nhật trạng thái thành công!');
    }
}
