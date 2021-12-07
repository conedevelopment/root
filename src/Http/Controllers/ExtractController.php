<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\ExtractRequest;
use Inertia\Inertia;
use Inertia\Response;

class ExtractController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Cone\Root\Http\Requests\ExtractRequest  $request
     * @return \Inertia\Response
     */
    public function __invoke(ExtractRequest $request): Response
    {
        $resource = $request->resource();

        $extract = $resource->findResolved($request, $request->route('reference'));

        return Inertia::render(
            'Resource/Index',
            $extract->toIndex($request)
        );
    }
}
