<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\ResourceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class RelationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ResourceRequest $request): JsonResponse
    {
        $field = $request->resolved();

        $model = $request->resource()->getModelInstance();

        $models = $field->resolveQuery($request, $model)
                        ->tap(static function (Builder $query) use ($request): void {
                            if ($query->hasNamedScope('filter')) {
                                $query->filter($request);
                            }
                        })
                        ->paginate()
                        ->through(static function (Model $related) use ($request, $model, $field): array {
                            return $field->mapOption($request, $model, $related);
                        });

        return new JsonResponse($models);
    }
}
