<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Extracts\Extract;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExtractController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Extract $extract): Response
    {
        return Inertia::render(
            'Extracts/Index',
            $extract->toIndex($request)
        );
    }
}
