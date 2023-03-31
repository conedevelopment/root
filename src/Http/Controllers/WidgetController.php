<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WidgetController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        return new Response($request->widget()->render());
    }
}
