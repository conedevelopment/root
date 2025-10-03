<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class MorphToController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Model $model, ...$params): Response
    {
        $field = $request->route('field');

        $related = array_filter($params, fn (mixed $param): bool => $param instanceof Model);

        $related = $related[array_key_last($related)] ?? null;

        $data = $field->toInput($request, $related ?: $model);

        return ResponseFactory::view($data['template'], $data);
    }
}
