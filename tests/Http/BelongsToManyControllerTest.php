<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Root;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class BelongsToManyControllerTest extends TestCase
{
    protected User $admin;

    protected BelongsToMany $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->field = Root::instance()
            ->resources
            ->resolve('users')
            ->resolveFields($this->app['request'])
            ->first(function ($field) {
                return $field->getModelAttribute() === 'teams';
            });
    }

    public function test_a_belongs_to_many_controller_handles_index(): void
    {
        $this->actingAs($this->admin)
            ->get('/root/users/'.$this->admin->getKey().'/fields/teams')
            ->assertOk()
            ->assertViewIs('root::resources.index')
            ->assertViewHas($this->field->toIndex($this->app['request'], $this->admin));
    }
}
