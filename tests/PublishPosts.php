<?php

namespace Cone\Root\Tests;

use Cone\Root\Actions\Action;
use Cone\Root\Fields\Text;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class PublishPosts extends Action
{
    public function handle(Request $request, Collection $models): void
    {
        //
    }

    public function fields(Request $request): array
    {
        return [
            Text::make('Title'),
        ];
    }

    public function routes(Router $router): void
    {
        $router->post('/', function () {
            //
        });
    }
}
