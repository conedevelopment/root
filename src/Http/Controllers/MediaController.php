<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $resource = $request->route('_resource');

        $field = $request->route('field');

        // Gate::allowIf($field->authorized($request, $model));

        $model = $request->filled('model')
            ? $resource->resolveRouteBinding($request, $request->input('model'))
            : $resource->getModelInstance();

        return match ($request->method()) {
            'GET' => new JsonResponse($field->paginate($request, $model)),
            'POST' => new JsonResponse($field->upload($request, $model), JsonResponse::HTTP_CREATED),
            'DELETE' => new JsonResponse(['deleted' => $field->prune($request, $model, $request->input('ids', []))]),
        };
    }
}
