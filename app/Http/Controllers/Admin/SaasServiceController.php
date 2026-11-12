<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SaasEnglishTranslator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class SaasServiceController extends Controller
{
    public function index(): View
    {
        $services = DB::table('saas_services')->orderBy('sort_order')->orderByDesc('id')->paginate(15);
        return view('admin.saas.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.saas.form', ['service' => null]);
    }

    public function store(Request $request, SaasEnglishTranslator $translator): RedirectResponse
    {
        $payload = $this->validatedPayload($request);
        $payload = $this->translateChangedFields($payload, null, $translator);
        $now = now();
        DB::table('saas_services')->insert($payload + ['created_at' => $now, 'updated_at' => $now]);
        return redirect()->route('admin.saas.index')->with('success', 'Đã thêm dịch vụ SaaS.');
    }

    public function edit(int $id): View
    {
        $service = DB::table('saas_services')->find($id);
        abort_if($service === null, 404);
        return view('admin.saas.form', compact('service'));
    }

    public function update(Request $request, int $id, SaasEnglishTranslator $translator): RedirectResponse
    {
        $service = DB::table('saas_services')->find($id);
        abort_if($service === null, 404);
        $payload = $this->validatedPayload($request, $id);
        $payload = $this->translateChangedFields($payload, $service, $translator);
        $payload['updated_at'] = now();
        DB::table('saas_services')->where('id', $id)->update($payload);
        return redirect()->route('admin.saas.index')->with('success', 'Đã cập nhật dịch vụ SaaS.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $service = DB::table('saas_services')->find($id);
        abort_if($service === null, 404);
        DB::table('saas_services')->where('id', $id)->update([
            'is_active' => ! (bool) $service->is_active, 'updated_at' => now(),
        ]);
        return redirect()->route('admin.saas.index')->with('success', 'Đã thay đổi trạng thái hiển thị.');
    }

    /** Dịch xem trước trên Admin, chỉ gọi qua POST có CSRF và quyền Admin. */
    public function translatePreview(Request $request, SaasEnglishTranslator $translator): JsonResponse
    {
        $data = $request->validate([
            'field' => ['required', Rule::in(['name_vi', 'summary_vi', 'description_vi'])],
            'text' => ['nullable', 'string', 'max:10000'],
        ]);
        try {
            $translated = $translator->translate($data['text'] ?? '');
            return response()->json(['translation' => $translated]);
        } catch (RuntimeException|\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json(['message' => 'Tạm thời không thể dịch. '.$e->getMessage()], 503);
        }
    }

    private function validatedPayload(Request $request, ?int $id = null): array
    {
        // Chấp nhận 199000 hoặc 199.000. Không cho dấu phẩy, số thập phân, số âm.
        $price = $request->input('price_monthly');
        if (is_string($price)) {
            $price = trim($price);
            if ($price === '') {
                $request->merge(['price_monthly' => null]);
            } elseif (preg_match('/^(?:[0-9]+|[0-9]{1,3}(?:\.[0-9]{3})+)$/D', $price)) {
                $request->merge(['price_monthly' => str_replace('.', '', $price)]);
            }
        }
        // Slug được chuẩn hóa cả trên server, không phụ thuộc JavaScript.
        if (! $request->filled('slug') && $request->filled('name_vi')) {
            $request->merge(['slug' => Str::slug((string) $request->input('name_vi'))]);
        }
        $payload = $request->validate([
            'name_vi' => ['required', 'string', 'max:150'],
            'slug' => [
                'required', 'string', 'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('saas_services', 'slug')->ignore($id),
            ],
            'summary_vi' => ['nullable', 'string', 'max:500'],
            'description_vi' => ['nullable', 'string', 'max:10000'],
            'price_monthly' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000000'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ], [
            'price_monthly.integer' => 'Giá tháng phải là số nguyên VND. Nhập 199000 hoặc 199.000, không nhập số thập phân.',
            'price_monthly.min' => 'Giá tháng không được âm.',
            'price_monthly.max' => 'Giá tháng vượt quá giới hạn cho phép.',
            'slug.unique' => 'Đường dẫn URL đã tồn tại. Hãy đổi slug khác.',
            'slug.regex' => 'Slug chỉ chứa chữ thường, số và dấu gạch ngang.',
        ]);
        $payload['is_active'] = $request->boolean('is_active');
        $payload['is_featured'] = $request->boolean('is_featured');
        // Không nhận name_en/summary_en/description_en từ form để tránh sửa trái ý qua hidden field.
        return $payload;
    }

    private function translateChangedFields(array $payload, ?object $original, SaasEnglishTranslator $translator): array
    {
        foreach (['name_vi' => 'name_en', 'summary_vi' => 'summary_en', 'description_vi' => 'description_en'] as $source => $target) {
            $vi = trim((string) ($payload[$source] ?? ''));
            $oldVi = trim((string) ($original?->{$source} ?? ''));
            $oldEn = trim((string) ($original?->{$target} ?? ''));
            if ($vi === '') {
                $payload[$target] = null;
            } elseif ($original !== null && $vi === $oldVi && $oldEn !== '') {
                $payload[$target] = $original->{$target};
            } else {
                try {
                    $translated = $translator->translate($vi);
                    $max = $target === 'name_en' ? 150 : ($target === 'summary_en' ? 500 : 10000);
                    if ($translated === null || mb_strlen($translated, 'UTF-8') > $max) {
                        throw new RuntimeException('Bản dịch quá dài hoặc không hợp lệ. Hãy rút gọn nội dung tiếng Việt.');
                    }
                    $payload[$target] = $translated;
                } catch (RuntimeException|\Illuminate\Http\Client\ConnectionException $e) {
                    throw ValidationException::withMessages([
                        $source => 'Không thể dịch tự động sang tiếng Anh: '.$e->getMessage().' Hãy thử lại trước khi lưu.',
                    ]);
                }
            }
        }
        return $payload;
    }
}
