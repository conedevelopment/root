<?php

namespace Cone\Root\Tests\Widgets;

use Cone\Root\Tests\User;
use Cone\Root\Widgets\Trend;
use Illuminate\Database\Eloquent\Builder;

class UsersTrend extends Trend
{
    public function query(): Builder
    {
        return User::query();
    }
}
