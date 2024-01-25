<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;

class LoginControllerTest extends TestCase
{
    public function test_login_controller_shows_login_form(): void
    {
        $this->get('/root/login')
            ->assertOk()
            ->assertViewIs('root::auth.login');
    }

    public function test_login_controller_handles_login(): void
    {
        Event::fake([Login::class]);

        $this->post('/root/login')
            ->assertRedirect()
            ->assertSessionHasErrors(['email', 'password']);

        $user = User::factory()->create();

        $this->post('/root/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect()
            ->assertSessionDoesntHaveErrors();

        Event::assertDispatched(Login::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    public function test_login_controller_handles_logout(): void
    {
        Event::fake([Logout::class]);

        $user = User::factory()->create();

        $this->app['auth']->login($user);

        $this->post('/root/logout')
            ->assertRedirect();

        Event::assertDispatched(Logout::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }
}
