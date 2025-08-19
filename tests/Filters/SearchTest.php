<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Filters;

use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Fields;
use Cone\Root\Fields\Text;
use Cone\Root\Filters\Search;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class SearchTest extends TestCase
{
    public function test_a_search_filter_modifies_query(): void
    {
        $filter = new Search(new Fields([
            (new Text('Name'))->searchable(),
            (new BelongsToMany('Teams'))->searchable(),
        ]));

        $this->assertSame(
            'select * from "users" where "users"."deleted_at" is null',
            $filter->apply($this->app['request'], User::query(), null)->toRawSql()
        );

        $this->assertSame(
            'select * from "users" where ("users"."name" like \'%foo%\' or exists (select * from "teams" inner join "team_user" on "teams"."id" = "team_user"."team_id" where "users"."id" = "team_user"."user_id" and "teams"."id" like \'%foo%\')) and "users"."deleted_at" is null',
            $filter->apply($this->app['request'], User::query(), 'foo')->toRawSql()
        );
    }
}
