<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Http;

use Cone\Root\Root;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

final class DashboardControllerTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_dashboard_controller_handles_request(): void
    {
        $this->actingAs($this->admin)
            ->get('/root')
            ->assertViewIs('root::dashboard')
            ->assertViewHas([
                'widgets' => Root::instance()->widgets->toArray(),
            ]);
    }
}
