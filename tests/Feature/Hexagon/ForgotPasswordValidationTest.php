<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Tests\Support\HexagonIsolatedTestCase;

class ForgotPasswordValidationTest extends HexagonIsolatedTestCase
{
    public function test_unregistered_email_is_rejected_without_sending_link(): void
    {
        Notification::fake();
        $this->post(route('password.email'), [
            'email' => 'unregistered.hexagon@gmail.com',
            'hx_locale' => 'vi',
        ])->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    public function test_registered_customer_receives_reset_link(): void
    {
        Notification::fake();
        $customer = User::factory()->create([
            'email' => 'registered.hexagon@gmail.com',
            'is_admin' => false,
        ]);
        $this->post(route('password.email'), [
            'email' => 'registered.hexagon@gmail.com',
            'hx_locale' => 'vi',
        ])->assertSessionHas('status')->assertSessionHasNoErrors();
        Notification::assertSentTo($customer, ResetPassword::class);
    }

    public function test_admin_email_is_not_accepted_in_customer_reset_flow(): void
    {
        Notification::fake();
        User::factory()->create([
            'email' => 'admin.hexagon@gmail.com',
            'is_admin' => true,
        ]);
        $this->post(route('password.email'), [
            'email' => 'admin.hexagon@gmail.com',
            'hx_locale' => 'vi',
        ])->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    public function test_invalid_email_shows_inline_validation_error(): void
    {
        Notification::fake();
        $this->post(route('password.email'), [
            'email' => 'not-an-email',
            'hx_locale' => 'vi',
        ])->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    public function test_forgot_password_form_has_no_arrow_and_has_inline_error_area(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('hxForgotEmailError')
            ->assertDontSee('hx-submit-arrow');
    }
}
