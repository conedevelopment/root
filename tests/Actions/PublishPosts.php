<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Actions\Action;
use Cone\Root\Http\Requests\ActionRequest;
use Illuminate\Support\Collection;

class PublishPosts extends Action
{
    public function handle(ActionRequest $request, Collection $models): void
    {
        //
    }
}
