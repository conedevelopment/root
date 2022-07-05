<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\TemporaryJson;
use Cone\Root\Tests\TestCase;

class TemporaryJsonTest extends TestCase
{
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = new TemporaryJson();
    }

    /** @test */
    public function a_temporary_json_cannot_be_saved()
    {
        $this->assertFalse($this->model->save());
    }
}
