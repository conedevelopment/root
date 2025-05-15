<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Exceptions\SaveFormDataException;
use Cone\Root\Fields\Text;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class ActionTest extends TestCase
{
    protected SendNotification $action;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->action = new SendNotification;
    }

    public function test_an_action_has_key(): void
    {
        $this->assertSame('send-notification', $this->action->getKey());
    }

    public function test_an_action_has_name(): void
    {
        $this->assertSame('Send Notification', $this->action->getName());
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

    public function test_an_action_can_be_standalone(): void
    {
        $this->assertFalse($this->action->isStandalone());

        $this->action->standalone();

        $this->assertTrue($this->action->isStandalone());

        $this->action->standalone(false);

        $this->assertFalse($this->action->isStandalone());
    }

    public function test_an_action_registers_routes(): void
    {
        $this->app['router']->prefix('users/actions')->group(function ($router) {
            $this->action->registerRoutes($this->app['request'], $router);
        });

        $this->assertSame('/users/actions/send-notification', $this->action->getUri());

        $this->assertArrayHasKey(
            trim($this->action->getUri(), '/'),
            $this->app['router']->getRoutes()->get('POST')
        );
    }

    public function test_an_action_resolves_query(): void
    {
        $this->expectException(QueryResolutionException::class);

        $this->action->resolveQuery($this->app['request']);
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
            'modalKey' => 'action-send-notification',
            'name' => $this->action->getName(),
            'standalone' => false,
            'template' => 'root::actions.action',
        ], $this->action->toArray());
    }

    public function test_an_action_has_form_representation(): void
    {
        $model = new User;

        $this->assertSame(array_merge($this->action->toArray(), [
            'url' => null,
            'open' => false,
            'fields' => [],
        ]), $this->action->toForm($this->app['request'], $model));
    }

    public function test_an_action_has_response_representation(): void
    {
        $this->action->withQuery(fn () => User::query());

        $this->app['request']->merge(['models' => [$this->user->getKey()]]);
        $this->app['request']->setUserResolver(fn () => $this->user);

        $response = $this->createTestResponse(
            $this->action->perform($this->app['request']),
            $this->app['request']
        );

        $response->assertRedirect()
            ->assertSessionHas(sprintf('alerts.action-%s', $this->action->getKey()));
    }

    public function test_an_action_handles_exceptions_on_perform(): void
    {
        $this->expectException(SaveFormDataException::class);

        $this->createTestResponse(
            $this->action->perform($this->app['request']),
            $this->app['request']
        );
    }
}
