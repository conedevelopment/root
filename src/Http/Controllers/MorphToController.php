<?php

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
    public function __invoke(Request $request, Model $model): Response
    {
        $field = $request->route('field');

        $data = $field->toInput($request, $model);

        return ResponseFactory::view($data['template'], $data);
    }
}
