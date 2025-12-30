<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Actions;

use Cone\Root\Actions\SendPasswordResetNotification;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

final class SendPasswordResetNotificationTest extends TestCase
{
    public function test_send_password_reset_notifications_action(): void
    {
        Notification::fake();

        $action = new SendPasswordResetNotification;

        $user = User::factory()->create();

        $action->withQuery(fn () => User::query());

        $this->app['request']->merge(['models' => [$user->getKey()]]);

        $action->perform($this->app['request']);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
