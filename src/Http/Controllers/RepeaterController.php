<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RepeaterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        //

        return new JsonResponse();
    }
}
