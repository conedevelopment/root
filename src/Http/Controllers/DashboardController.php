<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Root;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFactory;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Root $root): Response
    {
        return ResponseFactory::view('root::dashboard', [
            'widgets' => $root->widgets->toArray(),
        ]);
    }
}
