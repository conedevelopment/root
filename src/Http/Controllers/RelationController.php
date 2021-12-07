<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Http\JsonResponse;

class RelationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(RootRequest $request): JsonResponse
    {
        $resource = $request->resource();

        $field = $resource->findResolved($request, $request->route('reference'));

        return new JsonResponse(
            $field->resolveOptions($request, $resource->getModelInstance())
        );
    }
}
