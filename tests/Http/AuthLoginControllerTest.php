<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class AuthLoginControllerTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_login_controller_show(): void
    {
        $this->get('/root/login')
            ->assertOk()
            ->assertViewIs('root::auth.login');
    }

    public function test_login_controller_login(): void
    {
        Event::fake([
            Login::class,
        ]);

        $user = User::factory()->unverified()->make();

        $this->post('/root/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertInvalid([
            'email' => __('auth.failed'),
        ]);

        $user->save();

        $this->post('/root/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors([
            'email' => __('auth.unverified'),
        ])->assertRedirect('/root/login');

        $user->markEmailAsVerified();

        $this->post('/root/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/root');

        Event::assertDispatched(Login::class, function (Login $event) use ($user) {
            return $user->is($event->user);
        });

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_controller_logout(): void
    {
        $this->app['auth']->login($this->user);

        $this->assertAuthenticatedAs($this->user);

        $this->post('/root/logout')
            ->assertRedirect('/root/login');

        $this->assertGuest();
    }
}
