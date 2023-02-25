<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Medium;
use Cone\Root\Models\Meta;
use Cone\Root\Tests\TestCase;
use Cone\Root\Traits\HasMeta;

class MetaTest extends TestCase
{
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = new class(Medium::factory()->make()->toArray()) extends Medium
        {
            use HasMeta;
        };

        $this->model->save();
    }

    /** @test */
    public function a_meta_belongs_to_a_metable()
    {
        $meta = Meta::factory()->make();

        $meta->metable()->associate($this->model)->save();

        $this->assertTrue($meta->metable->is($this->model));
    }

    /** @test */
    public function a_metable_model_has_metas()
    {
        $this->model->metas()->save(
            $meta = Meta::factory()->make()
        );

        $this->assertTrue($this->model->metas->first()->is($meta));
    }
}
