<?php

namespace Cone\Root\Tests\Http\Conrollers;

use Cone\Root\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class DashboardControllerTest extends TestCase
{
    /** @test */
    public function an_admin_can_invoke_dashboard()
    {
        $this->actingAs($this->admin)
            ->get(URL::route('root.dashboard'))
            ->assertOk()
            ->assertViewIs('root::app')
            ->assertViewHas([
                'page.component' => 'Dashboard',
                'page.props.widgets' => $this->app->make('root.widgets')->toArray(),
            ]);
    }
}
