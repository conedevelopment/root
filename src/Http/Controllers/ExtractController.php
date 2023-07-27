<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFactory;

class ExtractController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $extract = $request->route('rootExtract');

        // Gate::allowIf($extract->authorized($request));

        return ResponseFactory::view(
            'root::resource.extract',
            $extract->toIndex($request)
        );
    }
}
