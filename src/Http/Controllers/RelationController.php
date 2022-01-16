<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        $field = $request->route('resolved');

        $models = $field->resolveQuery($request, $request->resource()->getModelInstance())
                        ->tap(static function (Builder $query) use ($request): void {
                            if ($query->hasNamedScope('filter')) {
                                $query->filter($request);
                            }
                        })
                        ->cursorPaginate()
                        ->through(static function (Model $model) use ($request, $field) {
                            return [
                                'id' => $model->getKey(),
                                'label' => $field->resolveDisplay($request, $model),
                            ];
                        });

        return new JsonResponse($models);
    }
}
