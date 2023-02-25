<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RootRequest $request): Response
    {
        return Inertia::render('Dashboard', [
            'title' => __('Dashboard'),
            'widgets' => App::make('root.widgets')->available($request)->toArray(),
        ]);
    }
}
