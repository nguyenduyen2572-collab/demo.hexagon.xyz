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
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $search = is_string($search) ? mb_substr(trim($search), 0, 100) : '';
        $status = $request->query('status');
        $status = in_array($status, ['active', 'hidden'], true) ? $status : '';

        $query = DB::table('saas_services');
        if ($search !== '') {
            // ESCAPE trong LIKE: %, _ và \ phải được tìm như ký tự thông thường.
            $term = addcslashes($search, '%_\\');
            $query->where(function ($builder) use ($term) {
                $builder->where('name_vi', 'like', '%'.$term.'%')
                    ->orWhere('slug', 'like', '%'.$term.'%');
            });
        }
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'hidden') {
            $query->where('is_active', false);
        }
        $services = $query->orderBy('sort_order')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.saas.index', compact('services', 'search', 'status'));
    }

    public function create(): View
    {
        return view('admin.saas.form', ['service' => null]);
    }

    public function store(Request $request, SaasEnglishTranslator $translator): RedirectResponse
    {
        $payload = $this->validatedPayload($request);
        [$payload, $isDraft] = $this->resolveEnglishFields($payload, null, $translator, $request);
        $now = now();
        DB::table('saas_services')->insert($payload + ['created_at' => $now, 'updated_at' => $now]);
        return redirect()->route('admin.saas.index')->with(
            $isDraft ? 'warning' : 'success',
            $isDraft ? $this->draftSavedMessage() : 'Đã thêm dịch vụ SaaS.'
        );
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
        [$payload, $isDraft] = $this->resolveEnglishFields($payload, $service, $translator, $request);
        $payload['updated_at'] = now();
        DB::table('saas_services')->where('id', $id)->update($payload);
        return redirect()->route('admin.saas.index', $this->listQueryParameters($request))->with(
            $isDraft ? 'warning' : 'success',
            $isDraft ? $this->draftSavedMessage() : 'Đã cập nhật dịch vụ SaaS.'
        );
    }

    public function toggle(Request $request, int $id): RedirectResponse
    {
        $service = DB::table('saas_services')->find($id);
        abort_if($service === null, 404);
        if (! $service->is_active && ! $this->hasCompleteEnglishFields($service)) {
            return redirect()->route('admin.saas.index', $this->listQueryParameters($request))
                ->with('warning', 'Chưa thể hiển thị dịch vụ vì thiếu bản dịch tiếng Anh. Hãy chỉnh sửa dịch vụ để dịch lại hoặc nhập bản dịch thủ công.');
        }
        DB::table('saas_services')->where('id', $id)->update([
            'is_active' => ! (bool) $service->is_active, 'updated_at' => now(),
        ]);
        return redirect()->route('admin.saas.index', $this->listQueryParameters($request))
            ->with('success', 'Đã thay đổi trạng thái hiển thị.');
    }

    /** Chỉ mang theo tham số lọc an toàn, không nhận URL chuyển hướng từ client. */
    private function listQueryParameters(Request $request): array
    {
        $parameters = [];
        $search = $request->query('q');
        if (is_string($search)) {
            $search = mb_substr(trim($search), 0, 100);
            if ($search !== '') $parameters['q'] = $search;
        }
        $status = $request->query('status');
        if (in_array($status, ['active', 'hidden'], true)) $parameters['status'] = $status;
        $page = $request->query('page');
        if (is_string($page) && ctype_digit($page) && (int) $page >= 1 && (int) $page <= 1000000) {
            $parameters['page'] = (int) $page;
        }
        return $parameters;
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
            'translation_mode' => ['nullable', Rule::in(['auto', 'manual'])],
            'save_as_draft_on_translation_failure' => ['nullable', 'boolean'],
        ], [
            'price_monthly.integer' => 'Giá tháng phải là số nguyên VND. Nhập 199000 hoặc 199.000, không nhập số thập phân.',
            'price_monthly.min' => 'Giá tháng không được âm.',
            'price_monthly.max' => 'Giá tháng vượt quá giới hạn cho phép.',
            'slug.unique' => 'Đường dẫn URL đã tồn tại. Hãy đổi slug khác.',
            'slug.regex' => 'Slug chỉ chứa chữ thường, số và dấu gạch ngang.',
        ]);
        $payload['is_active'] = $request->boolean('is_active');
        $payload['is_featured'] = $request->boolean('is_featured');
        // Các tùy chọn hỗ trợ lưu chỉ dùng cho Controller, không ghi vào database.
        unset($payload['translation_mode'], $payload['save_as_draft_on_translation_failure']);
        // Chỉ nhận dữ liệu EN khi Admin chủ động chọn chế độ nhập thủ công.
        return $payload;
    }

    /**
     * @return array{0: array<string, mixed>, 1: bool} [payload, saved-as-hidden-draft]
     */
    private function resolveEnglishFields(
        array $payload,
        ?object $original,
        SaasEnglishTranslator $translator,
        Request $request
    ): array {
        $fields = [
            'name_vi' => ['name_en', 150],
            'summary_vi' => ['summary_en', 500],
            'description_vi' => ['description_en', 10000],
        ];

        if ($request->input('translation_mode', 'auto') === 'manual') {
            $translations = $request->validate([
                'name_en' => ['required', 'string', 'max:150'],
                'summary_en' => [trim((string) ($payload['summary_vi'] ?? '')) === '' ? 'nullable' : 'required', 'string', 'max:500'],
                'description_en' => [trim((string) ($payload['description_vi'] ?? '')) === '' ? 'nullable' : 'required', 'string', 'max:10000'],
            ], [
                'name_en.required' => 'Hãy nhập tên tiếng Anh để lưu bản dịch thủ công.',
                'summary_en.required' => 'Hãy nhập mô tả ngắn tiếng Anh hoặc xóa mô tả ngắn tiếng Việt.',
                'description_en.required' => 'Hãy nhập giới thiệu chi tiết tiếng Anh hoặc xóa nội dung tiếng Việt tương ứng.',
            ]);
            foreach ($fields as $vi => [$en]) {
                $payload[$en] = trim((string) ($payload[$vi] ?? '')) === ''
                    ? null : trim((string) ($translations[$en] ?? ''));
            }
            // Admin đã chủ động nhập đầy đủ bản dịch, không cần gọi API.
            return [$payload, false];
        }

        $allowDraft = $request->boolean('save_as_draft_on_translation_failure');
        $translationFailed = false;
        foreach ($fields as $viKey => [$enKey, $max]) {
            $vi = trim((string) ($payload[$viKey] ?? ''));
            $oldVi = trim((string) ($original?->{$viKey} ?? ''));
            $oldEn = trim((string) ($original?->{$enKey} ?? ''));

            if ($vi === '') {
                $payload[$enKey] = null;
            } elseif ($original !== null && $vi === $oldVi && $oldEn !== '') {
                $payload[$enKey] = $original->{$enKey};
            } elseif ($translationFailed) {
                // Sau một lỗi dịch, giữ những bản dịch cũ còn hợp lệ và để trống phần thay đổi.
                $payload[$enKey] = null;
            } else {
                try {
                    $translated = $translator->translate($vi);
                    if (! is_string($translated) || trim($translated) === '' || mb_strlen($translated, 'UTF-8') > $max) {
                        throw new RuntimeException('Bản dịch không hợp lệ hoặc vượt giới hạn.');
                    }
                    $payload[$enKey] = $translated;
                } catch (RuntimeException|\Illuminate\Http\Client\ConnectionException $e) {
                    if (! $allowDraft) {
                        throw ValidationException::withMessages([
                            $viKey => 'Dịch tiếng Anh tạm thời không thành công. Hãy thử lại, chọn lưu nháp khi dịch lỗi hoặc nhập bản dịch thủ công.',
                        ]);
                    }
                    $translationFailed = true;
                    $payload[$enKey] = null;
                }
            }
        }

        if ($translationFailed) {
            // Không cho công khai dữ liệu thiếu bản dịch (kể cả sửa dịch vụ đang bán).
            $payload['is_active'] = false;
        }
        return [$payload, $translationFailed];
    }

    private function hasCompleteEnglishFields(object $service): bool
    {
        foreach (['name_vi' => 'name_en', 'summary_vi' => 'summary_en', 'description_vi' => 'description_en'] as $vi => $en) {
            if (trim((string) ($service->{$vi} ?? '')) !== '' && trim((string) ($service->{$en} ?? '')) === '') {
                return false;
            }
        }
        return true;
    }

    private function draftSavedMessage(): string
    {
        return 'Đã lưu bản nháp ở trạng thái ẩn vì dịch tiếng Anh gặp lỗi. Mở Chỉnh sửa để dịch lại hoặc nhập bản dịch thủ công trước khi hiển thị.';
    }

}
