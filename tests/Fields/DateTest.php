<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Date;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Support\Facades\Date as DateFactory;

class DateTest extends TestCase
{
    protected Date $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Date('Created At');
    }

    public function test_a_date_field_has_date_type(): void
    {
        $this->assertSame('date', $this->field->getAttribute('type'));
        $this->assertSame(1, $this->field->getAttribute('step'));
    }

    public function test_date_has_min_max_attributes(): void
    {
        $this->assertNull($this->field->getAttribute('min'));

        $date = DateFactory::now();

        $this->field->min($date);
        $this->assertSame($date->format('Y-m-d'), $this->field->getAttribute('min'));

        $this->field->max($date);
        $this->assertSame($date->format('Y-m-d'), $this->field->getAttribute('max'));
    }

    public function test_a_date_field_has_time(): void
    {
        $model = new User;

        $now = DateFactory::now();

        $model->setAttribute('created_at', $now);

        $this->assertSame($now->format('Y-m-d'), $this->field->resolveFormat($this->app['request'], $model));

        $this->field->withTime();
        $this->assertSame('datetime-local', $this->field->getAttribute('type'));
        $this->assertSame($now->format('Y-m-d H:i:s'), $this->field->resolveFormat($this->app['request'], $model));
    }
}
