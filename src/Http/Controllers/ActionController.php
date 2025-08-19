<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        /** @var \Cone\Root\Actions\Action $action */
        $action = $request->route('action');

        return $action->perform($request);
    }
}
