<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Support\Facades\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Inertia\Response
     */
    public function index(Request $request, string $key): Response
    {
        $resource = Resource::resolve($key);

        return $resource->toIndexResponse($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Inertia\Response
     */
    public function create(Request $request, string $key): Response
    {
        $resource = Resource::resolve($key);

        return $resource->toCreateResponse($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, string $key): RedirectResponse
    {
        $resource = Resource::resolve($key);

        return $resource->toStoreResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function show(Request $request, string $key, string $id): Response
    {
        $resource = Resource::resolve($key);

        return $resource->toShowResponse($request, $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function edit(Request $request, string $key, string $id): Response
    {
        $resource = Resource::resolve($key);

        return $resource->toEditResponse($request, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $key, string $id): RedirectResponse
    {
        $resource = Resource::resolve($key);

        return $resource->toUpdateResponse($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, string $key, string $id): RedirectResponse
    {
        $resource = Resource::resolve($key);

        return $resource->toDestroyResponse($request, $id);
    }

    /**
     * Perform the action on the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action(Request $request, string $key): RedirectResponse
    {
        $resource = Resource::resolve($key);

        return $resource->handleAction($request);
    }
}
