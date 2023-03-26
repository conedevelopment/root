<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Root;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Root $root): Response
    {
        return Inertia::render('Dashboard', [
            'title' => __('Dashboard'),
            'widgets' => $root->widgets->toArray(),
        ]);
    }
}
