<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;

class SaasServiceController extends Controller
{
    public function index(): View
    {
        $services = DB::table('saas_services')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.saas.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.saas.form', ['service' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedPayload($request);
        $now = now();

        DB::table('saas_services')->insert($payload + [
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()->route('admin.saas.index')
            ->with('success', 'Đã thêm dịch vụ SaaS.');
    }

    public function edit(int $id): View
    {
        $service = DB::table('saas_services')->findOrFail($id);
        return view('admin.saas.form', compact('service'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        DB::table('saas_services')->findOrFail($id);
        $payload = $this->validatedPayload($request, $id);
        $payload['updated_at'] = now();

        DB::table('saas_services')->where('id', $id)->update($payload);

        return redirect()->route('admin.saas.index')
            ->with('success', 'Đã cập nhật dịch vụ SaaS.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $service = DB::table('saas_services')->findOrFail($id);

        DB::table('saas_services')->where('id', $id)->update([
            'is_active' => ! (bool) $service->is_active,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.saas.index')
            ->with('success', 'Đã thay đổi trạng thái hiển thị.');
    }

    private function validatedPayload(Request $request, ?int $id = null): array
    {
        $payload = $request->validate([
            'name_vi' => ['required', 'string', 'max:150'],
            'name_en' => ['nullable', 'string', 'max:150'],
            'slug' => [
                'required', 'string', 'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('saas_services', 'slug')->ignore($id),
            ],
            'summary_vi' => ['nullable', 'string', 'max:500'],
            'summary_en' => ['nullable', 'string', 'max:500'],
            'description_vi' => ['nullable', 'string', 'max:10000'],
            'description_en' => ['nullable', 'string', 'max:10000'],
            'price_monthly' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000000'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ]);

        // Never trust hidden form fields to decide mass-assignment behavior.
        $payload['is_active'] = $request->boolean('is_active');
        $payload['is_featured'] = $request->boolean('is_featured');

        return $payload;
    }
}
