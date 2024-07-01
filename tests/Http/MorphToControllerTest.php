<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Fields\MorphTo;
use Cone\Root\Root;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class MorphToControllerTest extends TestCase
{
    protected MorphTo $field;

    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = Root::instance()
            ->resources
            ->resolve('users')
            ->resolveFields($this->app['request'])
            ->first(function ($field) {
                return $field->getModelAttribute() === 'employer';
            });

        $this->admin = User::factory()->create();
    }

    public function test_morph_to_controller_handles_request(): void
    {
        $this->actingAs($this->admin)
            ->get('/root/users/'.$this->admin->getKey().'/fields/employer')
            ->assertOk()
            ->assertViewIs('root::fields.morph-to')
            ->assertViewHas($this->field->toInput($this->app['request'], $this->admin));
    }
}
