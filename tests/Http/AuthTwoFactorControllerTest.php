<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Http;

use Cone\Root\Models\AuthCode;
use Cone\Root\Notifications\AuthCodeNotification;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Support\Facades\Notification;

final class AuthTwoFactorControllerTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email' => 'twofactor@root.local']);
    }

    public function test_two_factor_controller_show(): void
    {
        $this->actingAs($this->user)
            ->get('/root/two-factor')
            ->assertOk()
            ->assertViewIs('root::auth.two-factor');
    }

    public function test_two_factor_controller_resend(): void
    {
        Notification::fake();

        $this->actingAs($this->user)
            ->post('/root/two-factor/resend')
            ->assertRedirect('/root/two-factor')
            ->assertSessionHas('status', __('The authentication code has been sent!'));

        Notification::assertSentTo($this->user, AuthCodeNotification::class);
    }

    public function test_two_factor_controller_verify(): void
    {
        $code = AuthCode::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->post('/root/two-factor', [
                'code' => 000000,
            ])
            ->assertRedirect('/root/two-factor')
            ->assertSessionHasErrors([
                'code' => __('The authentication code is not valid!'),
            ]);

        $this->actingAs($this->user)
            ->post('/root/two-factor', [
                'code' => $code->code,
                'trust' => true,
            ])
            ->assertRedirect('/root')
            ->assertCookie('device_token')
            ->assertSessionHas('root.auth.two-factor', true);

        $this->assertDatabaseMissing('root_auth_codes', ['id' => $code->getKey()]);
    }
}
