<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminSecurityV4Test extends HexagonIsolatedTestCase
{
    private function createAdmin(): User
    {
        return User::factory()->create([
            'email' => 'admin-security-v4@example.test',
            'is_admin' => true,
            'password' => Hash::make('CurrentSecure1!Pass'),
        ]);
    }

    private function updatePayload(array $overrides = []): array
    {
        return array_merge([
            'current_password' => 'CurrentSecure1!Pass',
            'new_password' => 'StrongerSecure2!Pass',
            'new_password_confirmation' => 'StrongerSecure2!Pass',
        ], $overrides);
    }

    public function test_admin_can_open_password_page_and_change_password_then_log_in_again(): void
    {
        $admin = $this->createAdmin();
        $this->post(route('admin.login.submit'), ['email' => $admin->email, 'password' => 'CurrentSecure1!Pass'])
            ->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.security.password.edit'))->assertOk()->assertSee('Đổi mật khẩu');
        $this->patch(route('admin.security.password.update'), $this->updatePayload())
            ->assertRedirect(route('login'))->assertSessionHas('status');

        $this->assertTrue(Hash::check('StrongerSecure2!Pass', $admin->fresh()->password));
        $this->assertFalse(Auth::guard('admin')->check());
        $this->post(route('admin.login.submit'), ['email' => $admin->email, 'password' => 'CurrentSecure1!Pass'])
            ->assertSessionHasErrors('email');
        $this->post(route('admin.login.submit'), ['email' => $admin->email, 'password' => 'StrongerSecure2!Pass'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_wrong_current_password_and_mismatch_do_not_change_saved_password(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin, 'admin');
        $this->patch(route('admin.security.password.update'), $this->updatePayload(['current_password' => 'BadPassword']))
            ->assertSessionHasErrors('current_password');
        $this->patch(route('admin.security.password.update'), $this->updatePayload(['new_password_confirmation' => 'Incorrect2!Pass']))
            ->assertSessionHasErrors('new_password');
        $this->assertTrue(Hash::check('CurrentSecure1!Pass', $admin->fresh()->password));
    }

    public function test_weak_or_reused_password_is_rejected(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin, 'admin');
        $this->patch(route('admin.security.password.update'), $this->updatePayload([
            'new_password' => 'weakpass', 'new_password_confirmation' => 'weakpass',
        ]))->assertSessionHasErrors('new_password');
        $this->patch(route('admin.security.password.update'), $this->updatePayload([
            'new_password' => 'CurrentSecure1!Pass', 'new_password_confirmation' => 'CurrentSecure1!Pass',
        ]))->assertSessionHasErrors('new_password');
        $this->assertTrue(Hash::check('CurrentSecure1!Pass', $admin->fresh()->password));
    }

    public function test_guest_and_customer_cannot_access_admin_password_page(): void
    {
        $this->get(route('admin.security.password.edit'))->assertRedirect(route('login'));
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer, 'web');
        $this->get(route('admin.security.password.edit'))->assertForbidden();
        $this->patch(route('admin.security.password.update'), $this->updatePayload())->assertForbidden();
    }

    public function test_password_update_preserves_separate_customer_session_and_cart(): void
    {
        $admin = $this->createAdmin();
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer, 'web');
        $this->actingAs($admin, 'admin');
        $this->withSession(['hexagon_cart' => ['hello.com' => ['domain' => 'hello.com']]]);
        $this->patch(route('admin.security.password.update'), $this->updatePayload())->assertRedirect(route('login'));
        $this->assertFalse(Auth::guard('admin')->check());
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($customer->id, Auth::guard('web')->id());
        $this->assertSame(['hello.com' => ['domain' => 'hello.com']], session('hexagon_cart'));
    }

    public function test_idle_admin_session_is_expired_without_logging_out_customer(): void
    {
        $admin = $this->createAdmin();
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer, 'web');
        $this->actingAs($admin, 'admin');
        $this->withSession(['hexagon_admin_last_activity_at' => time() - 1900]);
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->assertFalse(Auth::guard('admin')->check());
        $this->assertTrue(Auth::guard('web')->check());
    }

    public function test_admin_password_hash_change_invalidates_stamped_session(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin, 'admin');
        $this->withSession([
            'hexagon_admin_password_stamp' => hash('sha256', $admin->getAuthPassword()),
            'hexagon_admin_last_activity_at' => time(),
        ]);
        $admin->forceFill(['password' => 'DifferentSecure3!Pass'])->save();
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->assertFalse(Auth::guard('admin')->check());
    }
}
