<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\AuthCode;
use Cone\Root\Models\Medium;
use Cone\Root\Models\Notification;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class UserTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_a_user_has_uploads(): void
    {
        $medium = $this->user->uploads()->save(
            Medium::factory()->make()
        );

        $this->assertTrue($this->user->uploads->contains($medium));
    }

    public function test_a_user_has_notifications(): void
    {
        $notification = $this->user->rootNotifications()->save(
            Notification::factory()->make()
        );

        $this->assertTrue($this->user->rootNotifications->contains($notification));
    }

    public function test_a_user_has_avatar(): void
    {
        $this->assertNull((new User())->avatar);

        $this->assertNotNull($this->user->avatar);
    }

    public function test_a_user_has_auth_codes(): void
    {
        $code = $this->user->authCodes()->save(
            AuthCode::factory()->make()
        );

        $this->assertTrue($this->user->authCodes->contains($code));

        $this->assertTrue($this->user->authCode->is($code));
    }
}
