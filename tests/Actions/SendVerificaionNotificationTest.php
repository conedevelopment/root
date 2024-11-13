<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Actions\SendVerificationNotification;
use Cone\Root\Notifications\VerifyEmail;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Support\Facades\Notification;

class SendVerificaionNotificationTest extends TestCase
{
    public function test_send_verification_notifications_action(): void
    {
        Notification::fake();

        $action = new SendVerificationNotification;

        $user = User::factory()->create();

        $action->withQuery(fn () => User::query());

        $this->app['request']->merge(['models' => [$user->getKey()]]);

        $action->perform($this->app['request']);

        Notification::assertNotSentTo($user, VerifyEmail::class);
    }
}
