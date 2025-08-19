<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFactory;

class WidgetController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        /** @var \Cone\Root\Widgets\Widget $widget */
        $widget = $request->route('widget');

        $data = $widget->data($request);

        return ResponseFactory::view($data['template'], $data);
    }
}
