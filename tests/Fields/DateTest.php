<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Date;
use Cone\Root\Tests\Post;
use Cone\Root\Tests\TestCase;
use Illuminate\Support\Facades\Date as DateFactory;

class DateTest extends TestCase
{
    protected $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Date('Created At');
    }

    /** @test */
    public function a_date_field_has_datetime_component()
    {
        $this->assertSame('DateTime', $this->field->getComponent());
    }

    /** @test */
    public function a_date_field_has_time()
    {
        $model = new Post();

        $now = DateFactory::now();

        $model->setAttribute('created_at', $now);

        $this->assertSame($now->format('Y-m-d'), $this->field->resolveFormat($this->app['request'], $model));

        $this->field->withTime();

        $this->assertSame($now->format('Y-m-d H:i:s'), $this->field->resolveFormat($this->app['request'], $model));
    }

    /** @test */
    public function a_date_field_has_timezone()
    {
        $model = new Post();

        $now = DateFactory::now();

        $model->setAttribute('created_at', $now);

        $this->assertSame($now->format('Y-m-d'), $this->field->resolveFormat($this->app['request'], $model));

        $this->field->withTime()->timezone('Europe/Budapest');

        $this->assertSame(
            $now->tz('Europe/Budapest')->format('Y-m-d H:i:s'),
            $this->field->resolveFormat($this->app['request'], $model)
        );
    }
}
