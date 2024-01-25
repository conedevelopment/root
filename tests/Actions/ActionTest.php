<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Fields\Text;
use Cone\Root\Tests\SendPasswordResetNotification;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class ActionTest extends TestCase
{
    protected SendPasswordResetNotification $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = new SendPasswordResetNotification();

        $this->action->setQuery(User::query());
    }

    public function test_an_action_has_key(): void
    {
        $this->assertSame('send-password-reset-notification', $this->action->getKey());
    }

    public function test_an_action_has_name(): void
    {
        $this->assertSame('Send Password Reset Notification', $this->action->getName());
    }

    public function test_an_action_can_be_destructive(): void
    {
        $this->assertFalse($this->action->isDestructive());

        $this->action->destructive();

        $this->assertTrue($this->action->isDestructive());

        $this->action->destructive(false);

        $this->assertFalse($this->action->isDestructive());
    }

    public function test_an_action_can_be_confirmable(): void
    {
        $this->assertFalse($this->action->isConfirmable());

        $this->action->confirmable();

        $this->assertTrue($this->action->isConfirmable());

        $this->action->confirmable(false);

        $this->assertFalse($this->action->isConfirmable());
    }

    public function test_an_action_registers_routes(): void
    {
        $this->app['router']->prefix('users/actions')->group(function ($router) {
            $this->action->registerRoutes($this->app['request'], $router);
        });

        $this->assertSame('/users/actions/send-password-reset-notification', $this->action->getUri());

        $this->assertArrayHasKey(
            trim($this->action->getUri(), '/'),
            $this->app['router']->getRoutes()->get('POST')
        );
    }

    public function test_an_action_resolves_fields(): void
    {
        $this->action->withFields(function () {
            return [
                Text::make(__('Name')),
            ];
        });

        $this->assertTrue(
            $this->action->resolveFields($this->app['request'])->isNotEmpty()
        );
    }

    public function test_an_action_has_array_representation(): void
    {
        $this->assertSame([
            'confirmable' => $this->action->isConfirmable(),
            'destructive' => $this->action->isDestructive(),
            'key' => $this->action->getKey(),
            'modalKey' => 'action-send-password-reset-notification',
            'name' => $this->action->getName(),
            'template' => 'root::actions.action',
            'url' => $this->action->getUri(),
        ], $this->action->toArray());
    }

    public function test_an_action_has_form_representation(): void
    {
        $model = new User();

        $fields = $this->action
            ->resolveFields($this->app['request'])
            ->mapToInputs($this->app['request'], $model);

        $this->assertSame(array_merge($this->action->toArray(), [
            'open' => false,
            'fields' => [],
        ]), $this->action->toForm($this->app['request'], $model));
    }

    public function test_an_action_has_response_representation(): void
    {
        $response = $this->createTestResponse($this->action->perform($this->app['request']));

        $response->assertRedirect()
            ->assertSessionHas(sprintf('alerts.action-%s', $this->action->getKey()));
    }
}
