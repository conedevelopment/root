<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Support\Facades\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ExtractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $resourceKey
     * @param  string  $key
     * @return \Inertia\Response
     */
    public function index(Request $request, string $resourceKey, string $key): Response
    {
        $resource = Resource::resolve($resourceKey);

        $extract = $resource->resolveExtracts($request)->resolve($key);

        return $extract->toIndex($request, $resource);
    }

    /**
     * Perform the action on the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $resourecKey
     * @param  string  $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action(Request $request, string $resourceKey, string $key): RedirectResponse
    {
        $resource = Resource::resolve($resourceKey);

        $extract = $resource->resolveExtracts($request)->resolve($key);

        return $extract->handleAction($request, $resource);
    }
}
