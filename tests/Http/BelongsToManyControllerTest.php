<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Root;
use Cone\Root\Tests\Team;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class BelongsToManyControllerTest extends TestCase
{
    protected User $admin;

    protected Team $team;

    protected BelongsToMany $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->team = Team::factory()->create();

        $this->admin->teams()->attach([
            $this->team->getKey() => ['role' => 'admin'],
        ]);

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

    public function test_a_belongs_to_many_controller_handles_store(): void
    {
        $team = Team::factory()->create();

        $this->actingAs($this->admin)
            ->post('/root/users/'.$this->admin->getKey().'/fields/teams', [
                'related' => $team->getKey(),
                'role' => 'member',
            ])
            ->assertRedirect()
            ->assertSessionHas('alerts.relation-created');

        $this->assertDatabaseHas('team_user', [
            'user_id' => $this->admin->getKey(),
            'team_id' => $team->getKey(),
            'role' => 'member',
        ]);
    }

    public function test_a_belongs_to_many_controller_handles_update(): void
    {
        $team = $this->admin->teams->first();

        $this->assertSame('admin', $team->pivot->role);

        $this->actingAs($this->admin)
            ->patch('/root/users/'.$this->admin->getKey().'/fields/teams/'.$team->pivot->getKey(), [
                'related' => $team->getKey(),
                'role' => 'member',
            ])
            ->assertRedirect()
            ->assertSessionHas('alerts.relation-updated');

        $this->assertSame('member', $team->pivot->refresh()->role);
    }

    public function test_a_belongs_to_many_controller_handles_destroy(): void
    {
        $team = $this->admin->teams->first();

        $this->actingAs($this->admin)
            ->delete('/root/users/'.$this->admin->getKey().'/fields/teams/'.$team->pivot->getKey())
            ->assertRedirect()
            ->assertSessionHas('alerts.relation-deleted');

        $this->assertDatabaseMissing('team_user', ['id' => $team->pivot->getKey()]);
    }
}
