<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Fields\Repeater;
use Cone\Root\Root;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class RepeaterControllerTest extends TestCase
{
    protected Repeater $field;

    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = Root::instance()
            ->resources
            ->resolve('users')
            ->resolveFields($this->app['request'])
            ->first(function ($field) {
                return $field->getModelAttribute() === 'settings';
            });

        $this->admin = User::factory()->create();
    }

    public function test_repeater_controller_handles_request(): void
    {
        $this->actingAs($this->admin)
            ->post('/root/users/'.$this->admin->getKey().'/fields/settings')
            ->assertOk();
    }
}
