<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class WidgetController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        /** @var \Cone\Root\Widgets\Widget $widget */
        $widget = $request->route('widget');

        // Gate::allowIf($widget->authorized($request));

        return $widget->toResponse($request);
    }
}
