<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RepeaterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $resource = $request->route('resource');

        $field = $request->route('field');

        // Gate::allowIf($field->authorized($request, $model));

        $model = $request->filled('model')
            ? $resource->resolveRouteBinding($request, $request->input('model'))
            : $resource->getModelInstance();

        return new JsonResponse($field->buildOption($request, $model));
    }
}
