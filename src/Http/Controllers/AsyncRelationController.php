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
    public function __invoke(Request $request, Model $model): JsonResponse
    {
        $field = $request->route('field');

        return new JsonResponse($field->paginateRelatable($request, $model));
    }
}
