<?php

declare(strict_types=1);

namespace App\View\Components\Admin;

use App\Support\HexagonDashboardAnalytics;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/** Dashboard Analytics hoạt động độc lập, không yêu cầu đăng ký Service Provider. */
final class DashboardAnalytics extends Component
{
    /** @var array<string, mixed> */
    public array $hxAnalytics;

    public function __construct(HexagonDashboardAnalytics $analytics)
    {
        $this->hxAnalytics = $analytics->forDays(request()->query('days'));
    }

    public function render(): View
    {
        return view('admin.partials.analytics-dashboard-v2');
    }
}
