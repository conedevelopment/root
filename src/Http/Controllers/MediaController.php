<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class MediaController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var \Cone\Root\Resources\Resource $resource */
        $resource = $request->route('_resource');

        /** @var \Cone\Root\Fields\Media $field */
        $field = $request->route('field');

        $model = $request->filled('model')
            ? $resource->resolveRouteBinding($request, $request->input('model'))
            : $resource->getModelInstance();

        return match ($request->method()) {
            'GET' => new JsonResponse($field->paginateRelatable($request, $model)),
            'POST' => new JsonResponse($field->upload($request, $model), JsonResponse::HTTP_CREATED),
            'DELETE' => new JsonResponse(['deleted' => $field->prune($request, $model, $request->input('ids', []))]),
            default => throw new MethodNotAllowedHttpException(['GET', 'POST', 'DELETE']),
        };
    }
}
