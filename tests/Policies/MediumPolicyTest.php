<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Policies;

use Cone\Root\Models\Medium;
use Cone\Root\Policies\MediumPolicy;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

final class MediumPolicyTest extends TestCase
{
    protected MediumPolicy $policy;

    protected User $user;

    protected Medium $medium;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new MediumPolicy;
        $this->user = User::factory()->create();
        $this->medium = Medium::factory()->create();
    }

    public function test_user_can_view_any_media(): void
    {
        $this->assertTrue($this->policy->viewAny($this->user));
    }

    public function test_user_can_view_medium(): void
    {
        $this->assertTrue($this->policy->view($this->user, $this->medium));
    }

    public function test_user_can_create_medium(): void
    {
        $this->assertTrue($this->policy->create($this->user));
    }

    public function test_user_can_update_medium(): void
    {
        $this->assertTrue($this->policy->update($this->user, $this->medium));
    }

    public function test_user_can_delete_medium(): void
    {
        $this->assertTrue($this->policy->delete($this->user, $this->medium));
    }

    public function test_user_can_restore_medium(): void
    {
        $this->assertTrue($this->policy->restore($this->user, $this->medium));
    }

    public function test_user_can_force_delete_medium(): void
    {
        $this->assertTrue($this->policy->forceDelete($this->user, $this->medium));
    }
}
