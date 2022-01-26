<?php

namespace Cone\Root\Tests;

use Cone\Root\Actions\Action;
use Cone\Root\Http\Requests\ActionRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;

class PublishPosts extends Action
{
    public function handle(Request $request, Collection $models): void
    {
        //
    }

    public function perform(ActionRequest $request, Builder $query): RedirectResponse
    {
        $this->handle($request, $query->get());

        return Redirect::to('/posts');
    }

    public function routes(Router $router): void
    {
        $router->post($this->getKey(), function (ActionRequest $request) {
            return $this->perform($request, Post::query());
        });
    }
}
