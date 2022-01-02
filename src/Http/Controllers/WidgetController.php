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
        $widget = $request->route('resolved');

        return new Response($widget->render());
    }
}
