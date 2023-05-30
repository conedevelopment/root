<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Root;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Root $root): View
    {
        return ViewFactory::make('root::dashboard');
    }
}
