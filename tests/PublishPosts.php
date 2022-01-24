<?php

namespace Cone\Root\Tests;

use Cone\Root\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PublishPosts extends Action
{
    /**
     * Handle the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    public function handle(Request $request, Collection $models): void
    {
        //
    }
}
