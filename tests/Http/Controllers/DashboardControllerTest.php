<?php

namespace Cone\Root\Tests\Http\Controllers;

use Cone\Root\Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    /** @test */
    public function an_admin_can_invoke_dashboard()
    {
        $this->actingAs($this->admin)
            ->get('/root')
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Dashboard',
                'page.props.widgets' => $this->app->make('root')->widgets->toArray(),
            ]);
    }
}
