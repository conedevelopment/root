<?php

namespace Cone\Root\Tests\Filters;

use Cone\Root\Fields\Fields;
use Cone\Root\Fields\MorphTo;
use Cone\Root\Fields\Text;
use Cone\Root\Filters\Sort;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class SortTest extends TestCase
{
    public function test_a_sort_filter_modifies_query(): void
    {
        $filter = new Sort(new Fields([
            Text::make('Name')->sortable(),
            MorphTo::make('Employer')->sortable(column: 'name'),
        ]));

        $this->assertSame(
            'select * from "users" where "users"."deleted_at" is null',
            $filter->apply($this->app['request'], User::query(), ['by' => 'dummy'])->toRawSql()
        );

        $this->assertSame(
            'select * from "users" where "users"."deleted_at" is null order by "users"."name" desc',
            $filter->apply($this->app['request'], User::query(), ['by' => 'name'])->toRawSql()
        );

        $this->assertSame(
            'select * from "users" where "users"."deleted_at" is null order by (select "users"."name" from "users" where "users"."employer_id" = "users"."id" and "users"."deleted_at" is null) desc',
            $filter->apply($this->app['request'], User::query(), ['by' => 'employer'])->toRawSql()
        );
    }
}
