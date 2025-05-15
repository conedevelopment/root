<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Root;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Cone\Root\Widgets\Widget;

class WidgetControllerTest extends TestCase
{
    protected User $admin;

    protected Widget $widget;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->widget = Root::instance()
            ->resources
            ->resolve('users')
            ->resolveWidgets($this->app['request'])
            ->first(function ($widget) {
                return $widget->getKey() === 'users-count';
            });
    }

    public function test_widget_controller_handles_request(): void
    {
        $this->actingAs($this->admin)
            ->get('/root/resources/users/widgets/users-count')
            ->assertOk()
            ->assertViewIs($this->widget->getTemplate())
            ->assertViewHas($this->widget->data($this->app['request']));
    }
}
