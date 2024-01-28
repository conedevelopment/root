<?php

namespace Cone\Root\Tests\Resources;

use Cone\Root\Resources\Resource;
use Cone\Root\Tests\User;

class UserResource extends Resource
{
    protected string $model = User::class;
}
