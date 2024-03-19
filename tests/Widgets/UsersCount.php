<?php

namespace Cone\Root\Tests\Widgets;

use Cone\Root\Tests\User;
use Cone\Root\Widgets\Value;
use Illuminate\Database\Eloquent\Builder;

class UsersCount extends Value
{
    public function query(): Builder
    {
        return User::query();
    }
}
