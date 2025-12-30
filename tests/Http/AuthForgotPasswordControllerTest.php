<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Http;

use Cone\Root\Notifications\ResetPassword;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

final class AuthForgotPasswordControllerTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_forgot_password_controller_show(): void
    {
        $this->get('/root/password/reset')
            ->assertOk()
            ->assertViewIs('root::auth.forgot-password');
    }

    public function test_forgot_password_controller_send(): void
    {
        Notification::fake();

        $this->post('/root/password/email', [
            'email' => $this->user->email,
        ])->assertRedirect('/root/password/reset')
            ->assertSessionHas('status', __(Password::RESET_LINK_SENT));

        Notification::assertSentTo($this->user, ResetPassword::class);
    }
}
