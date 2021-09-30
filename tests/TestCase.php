<?php

namespace Cone\Root\Tests;

use Cone\Root\Models\User;
use Cone\Root\Tests\CreatesApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMix();

        Storage::fake('local');
        Storage::fake('public');

        Storage::disk('local')->makeDirectory('chunks');

        $this->admin = User::factory()->create();
    }
}
