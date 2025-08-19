<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Http;

use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;

class AuthResetPasswordControllerTest extends TestCase
{
    protected string $token;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->unverified()->create();

        $this->token = Password::broker()->createToken($this->user);
    }

    public function test_reset_password_controller_test(): void
    {
        $this->get('/root/password/reset/'.$this->token.'/'.$this->user->email)
            ->assertViewIs('root::auth.reset-password')
            ->assertViewHas([
                'token' => $this->token,
                'email' => $this->user->email,
            ]);
    }

    public function test_reset_password_controller_reset(): void
    {
        Event::fake([
            PasswordReset::class,
        ]);

        $this->post('/root/password/reset', [
            'email' => $this->user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'token' => $this->token,
        ])->assertRedirect('/root')
            ->assertSessionHas('status', __(Password::PASSWORD_RESET));

        Event::assertDispatched(PasswordReset::class, function (PasswordReset $event) {
            return $this->user->is($event->user);
        });

        $this->assertAuthenticatedAs($this->user);
    }
}
