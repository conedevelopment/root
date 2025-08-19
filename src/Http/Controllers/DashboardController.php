<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

use Cone\Root\Root;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFactory;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Root $root): Response
    {
        return ResponseFactory::view('root::dashboard', [
            'widgets' => $root->widgets->map->data($request)->all(),
        ]);
    }
}
