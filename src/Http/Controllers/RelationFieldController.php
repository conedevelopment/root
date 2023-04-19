<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelationFieldController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Model $model = null): JsonResponse
    {
        $field = $request->route('rootRelation');

        $model ??= $request->route('rootResource')->getModelInstance();

        $models = $field->resolveRelatableQuery($request, $model)
                        ->paginate()
                        ->setPath($field->getUri())
                        ->through(static function (Model $related) use ($request, $model, $field): array {
                            return $field->mapOption($request, $model, $related);
                        });

        return new JsonResponse($models);
    }
}
