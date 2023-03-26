<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExtractController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $extract = $request->route('rootExtract');

        return Inertia::render(
            'Extracts/Index',
            $extract->toIndex($request)
        );
    }
}
