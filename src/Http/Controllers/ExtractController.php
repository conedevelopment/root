<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\ExtractRequest;
use Inertia\Inertia;
use Inertia\Response;

class ExtractController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ExtractRequest $request): Response
    {
        return Inertia::render(
            'Extracts/Index',
            $request->extract()->toIndex($request)
        );
    }
}
