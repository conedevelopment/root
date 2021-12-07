<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Http\Response;

class WidgetController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(RootRequest $request): Response
    {
        $resource = $request->resource();

        $widget = $resource->findResolved($request, $request->route('reference'));

        return new Response($widget->render());
    }
}
