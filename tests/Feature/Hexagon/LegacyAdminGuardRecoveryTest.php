<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\Support\HexagonIsolatedTestCase;

final class LegacyAdminGuardRecoveryTest extends HexagonIsolatedTestCase
{
    public function test_legacy_web_admin_is_redirected_to_admin_login_instead_of_403(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'web')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertFalse(Auth::guard('admin')->check());
    }

    public function test_legacy_web_admin_can_log_out_without_403(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'web')
            ->post(route('admin.logout'))
            ->assertRedirect(route('login'));

        $this->assertFalse(Auth::guard('web')->check());
    }

    public function test_legacy_admin_login_moves_to_admin_guard_and_keeps_cart(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'email' => 'legacy-admin-guard@gmail.com',
            'password' => bcrypt('ExamplePassword!123'),
        ]);
        $cart = ['example.com' => ['domain' => 'example.com']];

        $this->actingAs($admin, 'web')->withSession(['hexagon_cart' => $cart]);
        $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'ExamplePassword!123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertFalse(Auth::guard('web')->check());
        $this->assertSame($cart, session('hexagon_cart'));
    }

    public function test_customer_still_cannot_visit_admin_dashboard(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);

        $this->actingAs($customer, 'web')
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
