<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\ActionRequest;
use Illuminate\Http\RedirectResponse;

class ActionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Cone\Root\Http\Requests\ActionRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(ActionRequest $request): RedirectResponse
    {
        $resource = $request->resource();

        $action = $request->route('resolved');

        return $action->perform(
            $request, $resource->query()
        );
    }
}
