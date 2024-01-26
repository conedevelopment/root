<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

class ForgotPasswordControllerTest extends TestCase
{
    public function test_forgot_password_controller_shows_form(): void
    {
        $this->get('/root/password/reset')
            ->assertOk()
            ->assertViewIs('root::auth.forgot-password');
    }

    public function test_forgot_password_controller_handles_password_reset_request(): void
    {
        Notification::fake([ResetPassword::class]);

        $this->post('/root/password/email')
            ->assertRedirect()
            ->assertSessionHasErrors(['email']);

        $user = User::factory()->create();

        $this->post('/root/password/email', [
            'email' => $user->email,
        ])->assertRedirect()
            ->assertSessionDoesntHaveErrors();

        Notification::assertNothingSentTo($user);
    }
}
