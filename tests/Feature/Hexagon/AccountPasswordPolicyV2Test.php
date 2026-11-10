<?php

declare(strict_types=1);

namespace Tests\Feature\Hexagon;

use App\Models\User;
use App\Support\HexagonPasswordPolicy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Tests\Support\HexagonIsolatedTestCase;

final class AccountPasswordPolicyV2Test extends HexagonIsolatedTestCase
{
    private function customer(): User
    {
        return User::factory()->create([
            'is_admin' => false,
            'email' => 'security-v2-customer@gmail.com',
            'password' => Hash::make('OldPass1!'),
        ]);
    }

    public function test_password_policy_accepts_eight_or_more_characters_and_rejects_weak_values(): void
    {
        $rules = ['required', 'string', ...HexagonPasswordPolicy::requirements()];
        $this->assertTrue(Validator::make(['password' => 'Ab1!cdef'], ['password' => $rules])->passes());
        $this->assertTrue(Validator::make(['password' => 'Ab1!cdefghijkl'], ['password' => $rules])->passes());
        foreach (['Ab1!cde', 'abcdefgh', 'ABCDEFGH1!', 'Abcdefgh1', 'abcdef1!'] as $weak) {
            $this->assertTrue(Validator::make(['password' => $weak], ['password' => $rules])->fails(), $weak);
        }
    }

    public function test_customer_can_change_password_using_eight_characters(): void
    {
        $user = $this->customer();
        $this->actingAs($user)
            ->patch(route('customer.account.password.update'), [
                'current_password' => 'OldPass1!',
                'new_password' => 'NewPass1!',
                'new_password_confirmation' => 'NewPass1!',
                'ui_language' => 'vi',
            ])
            ->assertRedirect(route('customer.account.index'))
            ->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPass1!', $user->fresh()->password));
    }

    public function test_password_change_rejects_wrong_current_password_and_confirmation(): void
    {
        $user = $this->customer();
        $this->actingAs($user)
            ->patch(route('customer.account.password.update'), [
                'current_password' => 'WrongPassword!',
                'new_password' => 'NewPass1!',
                'new_password_confirmation' => 'Other1!A',
                'ui_language' => 'vi',
            ])->assertSessionHasErrors(['current_password', 'new_password_confirmation']);
        $this->assertTrue(Hash::check('OldPass1!', $user->fresh()->password));
    }

    public function test_password_change_rejects_weak_password_and_uses_english_error(): void
    {
        $user = $this->customer();
        $this->actingAs($user)
            ->patch(route('customer.account.password.update'), [
                'current_password' => 'OldPass1!',
                'new_password' => 'abcdefgh',
                'new_password_confirmation' => 'abcdefgh',
                'ui_language' => 'en',
            ])->assertSessionHasErrors([
                'new_password' => 'Use uppercase, lowercase, a number and a special character.',
            ]);
        $this->assertTrue(Hash::check('OldPass1!', $user->fresh()->password));
    }

    public function test_password_reset_accepts_eight_characters_with_valid_token(): void
    {
        $user = $this->customer();
        $token = Password::createToken($user);
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPass1!',
            'password_confirmation' => 'NewPass1!',
            'ui_language' => 'vi',
        ])->assertRedirect(route('customer.login'))->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPass1!', $user->fresh()->password));
    }

    public function test_password_reset_mismatch_rejects_and_does_not_mutate_password(): void
    {
        $user = $this->customer();
        $this->post(route('password.update'), [
            'token' => Password::createToken($user),
            'email' => $user->email,
            'password' => 'NewPass1!',
            'password_confirmation' => 'Other1!A',
            'ui_language' => 'vi',
        ])->assertSessionHasErrors('password_confirmation');
        $this->assertTrue(Hash::check('OldPass1!', $user->fresh()->password));
    }

    public function test_customer_cannot_reuse_the_current_password(): void
    {
        $user = $this->customer();
        $this->actingAs($user)
            ->patch(route('customer.account.password.update'), [
                'current_password' => 'OldPass1!',
                'new_password' => 'OldPass1!',
                'new_password_confirmation' => 'OldPass1!',
                'ui_language' => 'vi',
            ])->assertSessionHasErrors('new_password');
        $this->assertTrue(Hash::check('OldPass1!', $user->fresh()->password));
    }

    public function test_admin_cannot_use_customer_change_password_form(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => Hash::make('OldPass1!'),
        ]);
        $this->actingAs($admin)->patch(route('customer.account.password.update'), [
            'current_password' => 'OldPass1!',
            'new_password' => 'NewPass1!',
            'new_password_confirmation' => 'NewPass1!',
        ])->assertForbidden();
        $this->assertTrue(Hash::check('OldPass1!', $admin->fresh()->password));
    }

    public function test_reset_page_shows_inline_field_errors_and_eight_character_minimum(): void
    {
        $this->get(route('password.reset', ['token' => 'fake-token', 'email' => 'security-v2-customer@gmail.com']))
            ->assertOk()
            ->assertSee('minlength="8"', false)
            ->assertSee('hx-security-inline-error');
    }
}
