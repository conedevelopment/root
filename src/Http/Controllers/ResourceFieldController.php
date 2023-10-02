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
        $model = $request->has('model')
            ? $resource->resolveRouteBinding($request, $request->query('model'))
            : $resource->getModelInstance();

        $form = $resource->toForm($request, $model);

        $field = $resource->findField(
            $request, $request->path()
        );

        if (is_null($field)) {
            throw new NotFoundHttpException();
        }

        return $field->toResponse($request);
    }
}
