<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\WidgetRequest;
use Illuminate\Http\Response;

class WidgetController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(WidgetRequest $request): Response
    {
        return new Response($request->widget()->render());
    }
}
