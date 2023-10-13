<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Resources\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResourceFieldController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Resource $resource): JsonResponse
    {
        $field = $resource->findField(
            $request, $request->path()
        );

        if (is_null($field)) {
            throw new NotFoundHttpException();
        }

        $model = $request->filled('model')
            ? $resource->resolveRouteBinding($request, $request->input('model'))
            : $resource->getModelInstance();

        return $field->handleApiRequest($request, $model);
    }
}
