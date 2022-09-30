<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\FieldsetModel;
use Cone\Root\Tests\TestCase;

class FieldsetModelTest extends TestCase
{
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = new FieldsetModel();
    }

    /** @test */
    public function a_fieldset_model_cannot_be_saved()
    {
        $this->assertFalse($this->model->save());
    }
}
