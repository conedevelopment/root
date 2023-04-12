<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Model $model = null): JsonResponse
    {
        $field = $request->route('rootRelation');

        $model ??= $request->route('rootResource')->getModelInstance();

        $models = $field->resolveQuery($request, $model)
                        ->tap(static function (Builder $query) use ($request): void {
                            if ($query->hasNamedScope('filter')) {
                                $query->filter($request);
                            }
                        })
                        ->paginate()
                        ->setPath($field->getUri())
                        ->through(static function (Model $related) use ($request, $model, $field): array {
                            return $field->mapOption($request, $model, $related);
                        });

        return new JsonResponse($models);
    }
}
