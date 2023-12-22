<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class MorphToController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Model $model, ?Model $related = null): Response
    {
        $field = $request->route('field');

        // Gate::allowIf($action->authorized($request));

        $data = $field->toInput($request, $related ?: $model);

        return ResponseFactory::view($data['template'], $data);
    }
}
