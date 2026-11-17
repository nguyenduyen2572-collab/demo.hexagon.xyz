<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use App\Support\HexagonOrderCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\HexagonIsolatedTestCase;

class HexagonPublicOrderCodeTest extends HexagonIsolatedTestCase
{
    private function makeOrder(?int $userId = null): object
    {
        $uuid = (string) Str::uuid();
        $id = DB::table('orders')->insertGetId([
            'order_code' => $uuid,
            'user_id' => $userId,
            'customer_name' => 'Khach hang HEXAGON',
            'customer_email' => 'buyer@example.test',
            'customer_phone' => '0900000000',
            'total_amount' => 260000,
            'status' => 'pending_demo',
            'is_mock' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('orders')->find($id);
    }

    public function test_public_code_is_stable_and_can_be_decoded(): void
    {
        $this->assertSame('HXG-01', HexagonOrderCode::fromId(1));
        $this->assertSame('HXG-23', HexagonOrderCode::fromId(23));
        $this->assertSame('HXG-1000000', HexagonOrderCode::fromId(1000000));
        $this->assertSame(23, HexagonOrderCode::toId('hxg-000023'));
        $this->assertNull(HexagonOrderCode::toId('HXG-000000'));
        $this->assertNull(HexagonOrderCode::toId('HXG-9999999999999999999'));
        $this->assertNull(HexagonOrderCode::toId('HXG-001'));
    }

    public function test_admin_can_search_by_public_code(): void
    {
        $order = $this->makeOrder();
        $public = HexagonOrderCode::forOrder($order);
        $query = \App\Support\HexagonAdminOrderFilters::apply(
            DB::table('orders'),
            \Illuminate\Http\Request::create('/admin/orders?q=' . urlencode($public), 'GET')
        );
        $this->assertSame([(int) $order->id], $query->pluck('id')->map(fn ($id) => (int) $id)->all());
    }

    public function test_customer_can_open_own_public_code_but_not_another_users(): void
    {
        $owner = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);
        $other = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);
        $order = $this->makeOrder($owner->id);
        $code = HexagonOrderCode::forOrder($order);

        $this->actingAs($owner)
            ->get(route('customer.account.orders.show', ['code' => $code]))
            ->assertOk()
            ->assertSee($code);

        $this->actingAs($other)
            ->get(route('customer.account.orders.show', ['code' => $code]))
            ->assertNotFound();

        // Old UUID URL is still supported for backward compatibility.
        $this->actingAs($owner)
            ->get(route('customer.account.orders.show', ['code' => $order->order_code]))
            ->assertOk();
    }

    public function test_public_receipt_requires_original_session_permission(): void
    {
        $order = $this->makeOrder();
        $code = HexagonOrderCode::forOrder($order);

        $this->withSession(['hexagon_order_codes' => []])
            ->get(route('orders.show', ['code' => $code]))
            ->assertForbidden();

        $this->withSession(['hexagon_order_codes' => [$order->order_code]])
            ->get(route('orders.show', ['code' => $code]))
            ->assertOk()
            ->assertSee($code);

        $this->withSession(['hexagon_order_codes' => [$order->order_code]])
            ->get(route('orders.show', ['code' => $order->order_code]))
            ->assertOk();
    }
}
