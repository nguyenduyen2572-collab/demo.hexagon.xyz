<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminCustomerInsightsTest extends HexagonIsolatedTestCase
{
    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function customer(string $name, string $email, bool $verified = false): User
    {
        return User::factory()->create([
            'is_admin' => false,
            'name' => $name,
            'email' => $email,
            'email_verified_at' => $verified ? now() : null,
        ]);
    }

    private function order(?int $userId, string $email, string $status = 'pending_demo', int $amount = 260000): void
    {
        $now = now();
        DB::table('orders')->insert([
            'order_code' => (string) Str::uuid(),
            'customer_name' => 'Khach dat hang',
            'customer_email' => $email,
            'customer_phone' => '0900000000',
            'total_amount' => $amount,
            'status' => $status,
            'is_mock' => true,
            'user_id' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function test_guest_and_customer_cannot_open_admin_customers(): void
    {
        $this->get(route('admin.customers.index'))->assertRedirect();
        $this->actingAs($this->customer('Thu A', 'khacha@example.com'))
            ->get(route('admin.customers.index'))->assertForbidden();
    }

    public function test_admin_can_search_by_name_and_email(): void
    {
        $this->customer('Nguyen Phuoc An', 'an.kh@example.com');
        $this->customer('Tran Ngoc Binh', 'binh.kh@example.com');

        $this->actingAs($this->admin())
            ->get(route('admin.customers.index', ['q' => 'Phuoc']))
            ->assertOk()->assertSee('Nguyen Phuoc An')->assertDontSee('Tran Ngoc Binh');

        $this->get(route('admin.customers.index', ['q' => 'binh.kh@example.com']))
            ->assertOk()->assertSee('Tran Ngoc Binh')->assertDontSee('Nguyen Phuoc An');
    }

    public function test_verification_filter_only_shows_matching_customers(): void
    {
        $this->customer('Duong Verified', 'verified@example.com', true);
        $this->customer('Duong Pending', 'pending@example.com', false);

        $this->actingAs($this->admin())
            ->get(route('admin.customers.index', ['verified' => 'no']))
            ->assertOk()->assertSee('Duong Pending')->assertDontSee('Duong Verified');
    }

    public function test_order_filter_and_totals_exclude_cancelled_amount(): void
    {
        $active = $this->customer('Minh Has Orders', 'hasorders@example.com');
        $this->customer('Mai No Orders', 'noorders@example.com');
        $this->order($active->id, $active->email, 'pending_demo', 260000);
        $this->order($active->id, $active->email, 'cancelled_demo', 500000);

        $this->actingAs($this->admin())
            ->get(route('admin.customers.index', ['orders' => 'yes']))
            ->assertOk()->assertSee('Minh Has Orders')->assertDontSee('Mai No Orders')
            ->assertSee('260.000');
    }

    public function test_guest_orders_are_not_attached_by_matching_email(): void
    {
        $customer = $this->customer('Khach Guest Mail', 'same@example.com');
        $this->order(null, $customer->email);

        $this->actingAs($this->admin())
            ->get(route('admin.customers.show', ['id' => $customer->id]))
            ->assertOk()->assertSee('Khach Guest Mail')
            ->assertSee('chưa có đơn hàng nào được liên kết');
    }

    public function test_customer_detail_only_displays_own_orders(): void
    {
        $first = $this->customer('Khach Detail One', 'first.details@example.com');
        $other = $this->customer('Khach Detail Two', 'other.details@example.com');
        $this->order($first->id, $first->email);
        $this->order($other->id, $other->email);

        $ownCode = DB::table('orders')->where('user_id', $first->id)->value('order_code');
        $otherCode = DB::table('orders')->where('user_id', $other->id)->value('order_code');

        $this->actingAs($this->admin())
            ->get(route('admin.customers.show', ['id' => $first->id]))
            ->assertOk()->assertSee($ownCode)->assertDontSee($otherCode);
    }

    public function test_admin_accounts_are_not_listed_as_customers(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)
            ->get(route('admin.customers.show', ['id' => $admin->id]))
            ->assertNotFound();
    }

    public function test_invalid_filter_does_not_trigger_sql_error(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.customers.index', ['sort' => 'not-a-valid-column']))
            ->assertSessionHasErrors('sort');
    }
}
