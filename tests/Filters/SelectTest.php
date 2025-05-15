<?php

namespace Cone\Root\Tests\Filters;

use Cone\Root\Filters\Select;
use Cone\Root\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SelectTest extends TestCase
{
    protected Select $filter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filter = new class extends Select
        {
            public function __construct()
            {
                $this->setKey('foo');
            }

            public function apply(Request $request, Builder $query, mixed $value): Builder
            {
                return $query;
            }

            public function options(Request $request): array
            {
                return [
                    'test' => 'value',
                    'foo' => 'bar',
                ];
            }
        };
    }

    public function test_a_select_filter_has_options(): void
    {
        $this->assertSame(
            ['test' => 'value', 'foo' => 'bar'],
            $this->filter->options($this->app['request'])
        );
    }

    public function test_a_select_filter_can_have_multiple_values(): void
    {
        $this->app['request']->merge(['foo' => 'value']);

        $this->assertFalse($this->filter->isMultiple());
        $this->assertSame(
            'value', $this->filter->getValue($this->app['request'])
        );

        $this->filter->multiple();

        $this->assertTrue($this->filter->isMultiple());
        $this->assertSame(
            ['value'], $this->filter->getValue($this->app['request'])
        );
    }
}
