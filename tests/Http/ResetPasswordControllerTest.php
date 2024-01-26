<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;

class ResetPasswordControllerTest extends TestCase
{
    protected User $user;

    protected string $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->unverified()->create();

        Password::broker()->sendResetLink(['email' => $this->user->email], function ($user, $token) {
            $this->token = $token;
        });
    }

    public function test_reset_password_controller_shows_form(): void
    {
        $this->get('/root/password/reset/'.$this->token.'/'.$this->user->email)
            ->assertOk()
            ->assertViewIs('root::auth.reset-password');
    }

    public function test_reset_password_controller_handles_password_reset_request(): void
    {
        Event::fake([PasswordReset::class]);

        $this->assertFalse($this->user->hasVerifiedEmail());

        $this->post('/root/password/reset')
            ->assertRedirect()
            ->assertSessionHasErrors(['email', 'password']);

        $this->post('/root/password/reset', [
            'token' => $this->token,
            'email' => $this->user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect()
            ->assertSessionDoesntHaveErrors();

        $this->assertTrue($this->user->refresh()->hasVerifiedEmail());

        Event::assertDispatched(PasswordReset::class, function ($event) {
            return $event->user->id === $this->user->id;
        });
    }
}
