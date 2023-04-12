<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PublishPosts extends Action
{
    public function handle(Request $request, Collection $models): void
    {
        //
    }
}
