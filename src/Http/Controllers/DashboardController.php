<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Root;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Root $root): Response
    {
        return Inertia::render('Dashboard', [
            'title' => __('Dashboard'),
            'widgets' => $root->widgets->available($root->request())->toArray(),
        ]);
    }
}
