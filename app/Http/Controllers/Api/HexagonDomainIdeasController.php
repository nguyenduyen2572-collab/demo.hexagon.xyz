<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HexagonGoDaddyDomainSuggestions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

final class HexagonDomainIdeasController extends Controller
{
    public function __invoke(Request $request, HexagonGoDaddyDomainSuggestions $service): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\pN\s-]+$/u'],
        ], [
            'q.required' => 'Vui lòng nhập từ khóa.',
            'q.min' => 'Nhập tối thiểu 2 ký tự để nhận gợi ý.',
            'q.max' => 'Từ khóa quá dài.',
            'q.regex' => 'Từ khóa chỉ được chứa chữ cái, số, dấu cách và dấu gạch ngang.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('q'),
            ], 422);
        }

        $query = trim((string) $request->input('q'));
        try {
            $items = $service->suggest($query);
        } catch (RuntimeException $e) {
            $code = in_array($e->getCode(), [502, 503], true) ? $e->getCode() : 503;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'items' => [],
            ], $code, ['Cache-Control' => 'no-store']);
        }

        return response()->json([
            'success' => true,
            'query' => $query,
            'source' => 'GoDaddy',
            'availability' => 'provider_suggested_not_final',
            'items' => $items,
        ]);
    }
}
