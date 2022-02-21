<?php

namespace Cone\Root\Tests;

use Cone\Root\Filters\SelectFilter;
use Illuminate\Http\Request;

class Type extends SelectFilter
{
    public function options(Request $request): array
    {
        return [];
    }
}
