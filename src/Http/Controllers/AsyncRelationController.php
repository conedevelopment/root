<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsyncRelationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Model $model, ...$params): JsonResponse
    {
        $field = $request->route('field');

        $related = array_filter($params, fn (mixed $param): bool => $param instanceof Model);

        $related = $related[array_key_last($related)] ?? null;

        return new JsonResponse($field->paginateRelatable($request, $related ?: $model));
    }
}
