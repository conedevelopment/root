<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Actions;

use Cone\Root\Actions\SendVerificationNotification;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

final class SendVerificationNotificationTest extends TestCase
{
    public function test_send_verification_notifications_action(): void
    {
        Notification::fake();

        $action = new SendVerificationNotification;

        $user = User::factory()->unverified()->create();

        $action->withQuery(fn () => User::query());

        $this->app['request']->merge(['models' => [$user->getKey()]]);

        $action->perform($this->app['request']);

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
