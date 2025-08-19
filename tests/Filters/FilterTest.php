<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Filters;

use Cone\Root\Filters\Filter;
use Cone\Root\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FilterTest extends TestCase
{
    protected Filter $filter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filter = new class extends Filter
        {
            public function __construct()
            {
                $this->setKey('foo');
            }

            public function getName(): string
            {
                return 'Test Filter';
            }

            public function apply(Request $request, Builder $query, mixed $value): Builder
            {
                return $query;
            }
        };
    }

    public function test_a_filter_has_keys(): void
    {
        $this->assertSame('foo', $this->filter->getKey());
        $this->assertSame('foo', $this->filter->getRequestKey());
    }

    public function test_a_filter_has_name(): void
    {
        $this->assertSame('Test Filter', $this->filter->getName());
    }

    public function test_a_filter_resolves_value_from_request(): void
    {
        $this->app['request']->merge(['foo' => 'value']);

        $this->assertSame(
            'value', $this->filter->getValue($this->app['request'])
        );
    }

    public function test_a_filter_can_be_active(): void
    {
        $this->app['request']->merge(['foo' => 'value']);

        $this->assertTrue($this->filter->isActive($this->app['request']));
    }
}
