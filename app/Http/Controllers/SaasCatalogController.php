<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class SaasCatalogController extends Controller
{
    public function index(): View
    {
        $services = DB::table('saas_services')
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12);

        return view('saas.index', compact('services'));
    }

    public function show(string $slug): View
    {
        $service = DB::table('saas_services')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('saas.show', compact('service'));
    }
}
