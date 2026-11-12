<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class SaasInquiryController extends Controller
{
    public const STATUSES = [
        'new' => 'Mới tiếp nhận',
        'contacted' => 'Đang tư vấn',
        'closed' => 'Đã hoàn tất',
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_keys(self::STATUSES))],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = DB::table('saas_inquiries');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['q'])) {
            $search = '%' . addcslashes(trim($filters['q']), '%_\\') . '%';
            $query->where(static function ($q) use ($search): void {
                $q->where('customer_name', 'like', $search)
                    ->orWhere('customer_email', 'like', $search)
                    ->orWhere('customer_phone', 'like', $search)
                    ->orWhere('service_name', 'like', $search);
            });
        }

        return view('admin.saas-inquiries.index', [
            'inquiries' => $query->orderByDesc('created_at')->orderByDesc('id')->paginate(15)->withQueryString(),
            'counts' => [
                'all' => DB::table('saas_inquiries')->count(),
                'new' => DB::table('saas_inquiries')->where('status', 'new')->count(),
                'contacted' => DB::table('saas_inquiries')->where('status', 'contacted')->count(),
                'closed' => DB::table('saas_inquiries')->where('status', 'closed')->count(),
            ],
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(int $id): View
    {
        $inquiry = DB::table('saas_inquiries')->find($id);
        abort_if($inquiry === null, 404);

        return view('admin.saas-inquiries.show', [
            'inquiry' => $inquiry,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $updated = DB::table('saas_inquiries')->where('id', $id)->update([
            'status' => $data['status'],
            'admin_note' => isset($data['admin_note']) ? trim($data['admin_note']) : null,
            'updated_at' => now(),
        ]);

        abort_if($updated === 0 && ! DB::table('saas_inquiries')->where('id', $id)->exists(), 404);

        return redirect()->route('admin.saas-inquiries.show', ['id' => $id])
            ->with('success', 'Đã cập nhật yêu cầu tư vấn.');
    }
}
