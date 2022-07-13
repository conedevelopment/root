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

        $this->model = new class(Medium::factory()->make()->toArray()) extends Medium {
            use HasMeta;

            protected $casts = [
                'metas.price' => 'float',
                'properties' => 'json',
            ];
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

    /** @test */
    public function a_metable_model_proxies_meta_attributes()
    {
        $this->assertNull($this->model->getAttribute('metas.price'));

        $this->model->setAttribute('metas.price', 10.3);

        $this->assertSame(10.3, $this->model->getAttribute('metas.price'));

        $this->model->save();

        $this->assertTrue(
            $this->model->metas->contains(fn ($meta) => $meta->key === 'price')
        );
    }
}
