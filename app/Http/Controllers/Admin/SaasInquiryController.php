<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\HexagonAdminRoles;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class SaasInquiryController extends Controller
{
    public const STATUSES = [
        'new' => 'Mới tiếp nhận',
        'contacted' => 'Đang tư vấn',
        'closed' => 'Đã hoàn tất',
    ];

    private const BUSINESS_TIMEZONE = 'Asia/Ho_Chi_Minh';

    public function index(Request $request): View
    {
        $employee = $request->user('admin');
        $isStaff = HexagonAdminRoles::role($employee) === 'staff';
        // Chỉ người đã được cấp quyền tư vấn và đang hoạt động mới được nhận việc.
        $admins = $this->eligibleAssignees();
        $assignedOptions = $isStaff ? ['mine'] : array_merge(
            ['mine', 'unassigned'],
            $admins->pluck('id')->map(static fn ($id): string => (string) $id)->all()
        );
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(self::STATUSES))],
            'q' => ['nullable', 'string', 'max:100'],
            'assigned' => ['nullable', Rule::in($assignedOptions)],
            'due' => ['nullable', Rule::in(['overdue', 'today', 'next7'])],
            'sort' => ['nullable', Rule::in(['priority', 'next', 'newest'])],
        ]);

        $query = DB::table('saas_inquiries')
            ->leftJoin('users as assigned_user', 'assigned_user.id', '=', 'saas_inquiries.assigned_admin_id')
            ->select('saas_inquiries.*', 'assigned_user.name as assigned_name');

        if ($isStaff) {
            $query->where('saas_inquiries.assigned_admin_id', $employee->id);
        }

        if (! empty($filters['status'])) {
            $query->where('saas_inquiries.status', $filters['status']);
        }
        if (! empty($filters['q'])) {
            $search = '%' . addcslashes(trim($filters['q']), '%_\\') . '%';
            $query->where(static function ($q) use ($search): void {
                $q->where('saas_inquiries.customer_name', 'like', $search)
                    ->orWhere('saas_inquiries.customer_email', 'like', $search)
                    ->orWhere('saas_inquiries.customer_phone', 'like', $search)
                    ->orWhere('saas_inquiries.service_name', 'like', $search);
            });
        }
        if (($filters['assigned'] ?? null) === 'mine') {
            $query->where('saas_inquiries.assigned_admin_id', auth('admin')->id());
        } elseif (($filters['assigned'] ?? null) === 'unassigned') {
            $query->whereNull('saas_inquiries.assigned_admin_id');
        } elseif (isset($filters['assigned']) && ctype_digit((string) $filters['assigned'])) {
            $query->where('saas_inquiries.assigned_admin_id', (int) $filters['assigned']);
        }

        $now = Carbon::now('UTC');
        $todayStart = Carbon::today(self::BUSINESS_TIMEZONE)->utc();
        $tomorrowStart = $todayStart->copy()->addDay();
        if (! empty($filters['due'])) {
            $query->where('saas_inquiries.status', '!=', 'closed')
                ->whereNotNull('saas_inquiries.follow_up_at');
            if ($filters['due'] === 'overdue') {
                $query->where('saas_inquiries.follow_up_at', '<', $now->format('Y-m-d H:i:s'));
            } elseif ($filters['due'] === 'today') {
                $query->where('saas_inquiries.follow_up_at', '>=', $todayStart->format('Y-m-d H:i:s'))
                    ->where('saas_inquiries.follow_up_at', '<', $tomorrowStart->format('Y-m-d H:i:s'));
            } else {
                $query->where('saas_inquiries.follow_up_at', '>=', $now->format('Y-m-d H:i:s'))
                    ->where('saas_inquiries.follow_up_at', '<', $now->copy()->addDays(7)->format('Y-m-d H:i:s'));
            }
        }

        // Ưu tiên lịch quá hạn, yêu cầu mới, lịch sắp tới; hoặc sắp xếp theo lựa chọn Admin.
        $sort = $filters['sort'] ?? 'priority';
        if ($sort === 'newest') {
            $query->orderByDesc('saas_inquiries.created_at');
        } elseif ($sort === 'next') {
            $query->orderByRaw("CASE WHEN saas_inquiries.status != 'closed' AND saas_inquiries.follow_up_at IS NOT NULL THEN 0 ELSE 1 END")
                ->orderBy('saas_inquiries.follow_up_at');
        } else {
            $query->orderByRaw("CASE
                WHEN saas_inquiries.status != 'closed' AND saas_inquiries.follow_up_at IS NOT NULL AND saas_inquiries.follow_up_at < ? THEN 0
                WHEN saas_inquiries.status = 'new' THEN 1
                WHEN saas_inquiries.status != 'closed' AND saas_inquiries.follow_up_at IS NOT NULL THEN 2
                WHEN saas_inquiries.status = 'contacted' THEN 3
                ELSE 4 END", [$now->format('Y-m-d H:i:s')])
                ->orderBy('saas_inquiries.follow_up_at');
        }

        // Bộ thống kê phải cùng phạm vi với bảng danh sách, không làm lộ tổng công ty.
        $scoped = DB::table('saas_inquiries');
        if ($isStaff) {
            $scoped->where('assigned_admin_id', $employee->id);
        }
        return view('admin.saas-inquiries.index', [
            'isStaff' => $isStaff,
            'inquiries'  => $query->orderByDesc('saas_inquiries.id')->paginate(15)->withQueryString(),
            'counts' => [
                'all' => (clone $scoped)->count(),
                'new' => (clone $scoped)->where('status', 'new')->count(),
                'contacted' => (clone $scoped)->where('status', 'contacted')->count(),
                'closed' => (clone $scoped)->where('status', 'closed')->count(),
                'overdue' => (clone $scoped)->where('status', '!=', 'closed')
                    ->whereNotNull('follow_up_at')->where('follow_up_at', '<', $now->format('Y-m-d H:i:s'))->count(),
            ],
            'statuses' => self::STATUSES,
            'admins' => $admins,
        ]);
    }

    public function show(Request $request, int $id): View
    {
        $inquiry = DB::table('saas_inquiries')
            ->leftJoin('users as assigned_user', 'assigned_user.id', '=', 'saas_inquiries.assigned_admin_id')
            ->select('saas_inquiries.*', 'assigned_user.name as assigned_name')
            ->where('saas_inquiries.id', $id)->first();
        abort_if($inquiry === null, 404);
        $employee = $request->user('admin');
        $isStaff = HexagonAdminRoles::role($employee) === 'staff';
        // Không phân biệt yêu cầu không tồn tại và yêu cầu thuộc nhân viên khác.
        abort_if($isStaff && (int) $inquiry->assigned_admin_id !== (int) $employee->id, 404);

        return view('admin.saas-inquiries.show', [
            'isStaff' => $isStaff,
            'inquiry' => $inquiry,
            'returnFilters' => $this->safeListFilters($request),
            'backToList' => route('admin.saas-inquiries.index', $this->safeListFilters($request)),
            'statuses' => self::STATUSES,
            'admins' => $isStaff ? collect() : $this->eligibleAssignees(),
            'activities' => DB::table('saas_inquiry_activities')
                ->where('saas_inquiry_id', $id)
                ->orderByDesc('id')->paginate(10, ['*'], 'activity_page'),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $employee = $request->user('admin');
        $isStaff = HexagonAdminRoles::role($employee) === 'staff';
        if ($isStaff && $request->exists('assigned_admin_id')) {
            abort(403, 'Nhân viên không có quyền phân công yêu cầu.');
        }
        $eligibleIds = $isStaff ? [] : $this->eligibleAssignees()->pluck('id')->all();
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:2000'],
            'assigned_admin_id' => ['nullable', 'integer', Rule::in($eligibleIds)],
            'follow_up_at' => ['nullable', 'date_format:Y-m-d\\TH:i'],
        ], [
            'assigned_admin_id.exists' => 'Người phụ trách phải là tài khoản Admin hợp lệ.',
            'follow_up_at.date_format' => 'Thời gian hẹn không đúng định dạng.',
        ]);

        $changed = DB::transaction(function () use ($data, $id, $isStaff, $employee): bool {
            $current = DB::table('saas_inquiries')->where('id', $id)->lockForUpdate()->first();
            abort_if($current === null, 404);
            abort_if($isStaff && (int) $current->assigned_admin_id !== (int) $employee->id, 404);

            $newStatus = $data['status'];
            $newAssignee = array_key_exists('assigned_admin_id', $data)
                ? ($data['assigned_admin_id'] === null ? null : (int) $data['assigned_admin_id'])
                : ($current->assigned_admin_id === null ? null : (int) $current->assigned_admin_id);
            $newFollowUp = array_key_exists('follow_up_at', $data)
                ? (empty($data['follow_up_at']) ? null : Carbon::createFromFormat(
                    '!Y-m-d\\TH:i', $data['follow_up_at'], self::BUSINESS_TIMEZONE
                )->utc()->format('Y-m-d H:i:s'))
                : $current->follow_up_at;

            // Completed requests have no pending follow-up reminder.
            if ($newStatus === 'closed') {
                $newFollowUp = null;
            }

            $oldAssignee = $current->assigned_admin_id === null ? null : (int) $current->assigned_admin_id;
            $normalizeDate = static fn ($date): ?string => $date === null
                ? null : Carbon::parse($date, 'UTC')->format('Y-m-d H:i:s');
            $oldFollowUp = $normalizeDate($current->follow_up_at);
            $newFollowUp = $normalizeDate($newFollowUp);
            $note = isset($data['admin_note']) ? trim($data['admin_note']) : '';
            $statusChanged = $current->status !== $newStatus;
            $assigneeChanged = $oldAssignee !== $newAssignee;
            $followUpChanged = $oldFollowUp !== $newFollowUp;
            $hasChanges = $statusChanged || $assigneeChanged || $followUpChanged || $note !== '';

            if (! $hasChanges) {
                return false;
            }

            $update = [
                'status' => $newStatus,
                'assigned_admin_id' => $newAssignee,
                'follow_up_at' => $newFollowUp,
                'updated_at' => now(),
            ];
            if ($note !== '') {
                // Retain the last note for compatibility; all new notes also go to the audit log.
                $update['admin_note'] = $note;
            }
            DB::table('saas_inquiries')->where('id', $id)->update($update);

            $assigneeNames = DB::table('users')
                ->whereIn('id', array_values(array_filter([$oldAssignee, $newAssignee], static fn ($value) => $value !== null)))
                ->pluck('name', 'id');

            DB::table('saas_inquiry_activities')->insert([
                'saas_inquiry_id' => $id,
                'admin_user_id' => auth('admin')->id(),
                'admin_name' => (string) (auth('admin')->user()?->name ?? 'Quản trị viên'),
                'status_from' => $statusChanged ? $current->status : null,
                'status_to' => $statusChanged ? $newStatus : null,
                'assignee_from' => $assigneeChanged ? ($assigneeNames[$oldAssignee] ?? 'Chưa phân công') : null,
                'assignee_to' => $assigneeChanged ? ($assigneeNames[$newAssignee] ?? 'Chưa phân công') : null,
                'follow_up_from' => $followUpChanged ? $oldFollowUp : null,
                'follow_up_to' => $followUpChanged ? $newFollowUp : null,
                'note' => $note !== '' ? $note : null,
                'created_at' => now(),
            ]);

            return true;
        });

        return redirect()->route('admin.saas-inquiries.show', ['id' => $id] + $this->safeListFilters($request))
            ->with($changed ? 'success' : 'info', $changed
                ? 'Đã cập nhật yêu cầu tư vấn và lưu lịch sử xử lý.'
                : 'Không có thay đổi mới để lưu.');
    }
    /**
     * Chỉ giữ tham số danh sách đã biết; không nhận URL chuyển hướng từ người dùng.
     * Được dùng ở trang chi tiết và sau khi cập nhật để tránh mất bộ lọc.
     */
    private function safeListFilters(Request $request): array
    {
        $isStaff = HexagonAdminRoles::role($request->user('admin')) === 'staff';
        $query = $request->query();
        $safe = [];
        foreach (['status' => array_keys(self::STATUSES),
                  'assigned' => $isStaff ? ['mine'] : ['mine', 'unassigned'],
                  'due' => ['overdue', 'today', 'next7'],
                  'sort' => ['priority', 'next', 'newest']] as $key => $allowed) {
            if (isset($query[$key]) && is_string($query[$key]) && in_array($query[$key], $allowed, true)) {
                $safe[$key] = $query[$key];
            }
        }
        // Giữ ID người phụ trách hợp lệ khi quay lại danh sách; bỏ qua ID bị giả mạo.
        $assigned = $query['assigned'] ?? null;
        if (! $isStaff && is_string($assigned) && preg_match('/^[1-9][0-9]{0,17}$/D', $assigned)
            && $this->eligibleAssignees()->contains('id', (int) $assigned)) {
            $safe['assigned'] = $assigned;
        }
        if (isset($query['q']) && is_string($query['q'])) {
            $search = trim($query['q']);
            if ($search !== '' && mb_strlen($search) <= 100) {
                $safe['q'] = $search;
            }
        }
        if (isset($query['page']) && is_string($query['page'])
            && preg_match('/^[1-9][0-9]{0,5}$/D', $query['page'])) {
            $safe['page'] = (int) $query['page'];
        }
        return $safe;
    }
    /** @return \Illuminate\Support\Collection<int, User> */
    private function eligibleAssignees(): \Illuminate\Support\Collection
    {
        return User::query()->where('is_admin', true)->orderBy('name')->orderBy('id')->get()
            ->filter(static fn (User $user): bool => HexagonAdminRoles::can($user, 'inquiries'))->values();
    }
}
