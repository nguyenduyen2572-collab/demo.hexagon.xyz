<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Tests\Support\HexagonIsolatedTestCase;

final class AdminRolesV90Test extends HexagonIsolatedTestCase
{
    private function admin(string $role='super_admin', array $perms=[]): User
    {
        $user=User::factory()->create(['is_admin'=>true]);
        $user->forceFill(['admin_role'=>$role,'admin_permissions'=>$perms,'admin_enabled'=>true])->save();
        return $user;
    }

    public function test_only_super_admin_can_create_accounts(): void
    {
        $super=$this->admin();
        $this->actingAs($super,'admin')->post(route('admin.team.store'),[
            'name'=>'Nhân viên SaaS', 'email'=>'employee.saas@hexagon.test',
            'password'=>'StrongPassword!123', 'password_confirmation'=>'StrongPassword!123',
            'role'=>'staff', 'permissions'=>['saas'],
        ])->assertRedirect(route('admin.team.index'));
        $this->assertDatabaseHas('users',['email'=>'employee.saas@hexagon.test','is_admin'=>1,'admin_role'=>'staff']);
        $this->assertSame(['saas'], User::where('email','employee.saas@hexagon.test')->first()->admin_permissions);
    }

    public function test_staff_cannot_be_granted_unsafe_permissions_by_forged_post(): void
    {
        $super=$this->admin();
        $this->actingAs($super,'admin')->post(route('admin.team.store'),[
            'name'=>'Không hợp lệ', 'email'=>'invalid.staff@hexagon.test',
            'password'=>'StrongPassword!123', 'password_confirmation'=>'StrongPassword!123',
            'role'=>'staff', 'permissions'=>['orders'],
        ])->assertSessionHasErrors('permissions');
        $this->assertDatabaseMissing('users',['email'=>'invalid.staff@hexagon.test']);
    }

    public function test_manager_cannot_access_team_or_unassigned_business_area(): void
    {
        $manager=$this->admin('manager',['orders']);
        $this->actingAs($manager,'admin')->get(route('admin.team.index'))->assertForbidden();
        $this->get(route('admin.domain-prices.index'))->assertForbidden();
        $this->get(route('admin.orders.index'))->assertOk();
    }

    public function test_staff_is_restricted_to_saas_area(): void
    {
        $staff=$this->admin('staff',['saas','orders']);
        $this->actingAs($staff,'admin')->get(route('admin.dashboard'))->assertOk()
            ->assertSee('Không gian làm việc');
        $this->get(route('admin.orders.index'))->assertForbidden();
        $this->get(route('admin.saas-inquiries.index'))->assertForbidden();
        $this->get(route('admin.saas.index'))->assertOk();
    }

    public function test_disabled_admin_cannot_continue_using_admin_routes(): void
    {
        $staff=$this->admin('staff',['saas']);
        $staff->forceFill(['admin_enabled'=>false])->save();
        $this->actingAs($staff,'admin')->get(route('admin.saas.index'))->assertRedirect(route('login'));
    }

    public function test_super_admin_cannot_be_demoted_by_team_form(): void
    {
        $super=$this->admin();
        $this->actingAs($super,'admin')->patch(route('admin.team.update',$super->id),[
            'name'=>'Different', 'role'=>'staff', 'permissions'=>['saas'],'admin_enabled'=>0,
        ])->assertForbidden();
        $this->assertDatabaseHas('users',['id'=>$super->id,'admin_role'=>'super_admin','admin_enabled'=>1]);
    }
}
