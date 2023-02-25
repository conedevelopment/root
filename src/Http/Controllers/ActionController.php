<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\ActionRequest;
use Illuminate\Http\RedirectResponse;

class ActionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ActionRequest $request): RedirectResponse
    {
        return $request->action()->perform($request);
    }
}
