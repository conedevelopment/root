<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Support\Facades\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        $resource = Resource::resolveFromRequest($request);

        return Inertia::render(
            'Resource/Index',
            $resource->toIndex($request)
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request): Response
    {
        $resource = Resource::resolveFromRequest($request);

        return Inertia::render(
            'Resource/Create',
            $resource->toCreate($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $resource = Resource::resolveFromRequest($request);

        $model = $resource->handleStore($request);

        return Redirect::route('root.resource.show', [$resource->getKey(), $model]);
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request): Response
    {
        $resource = Resource::resolveFromRequest($request);

        return Inertia::render(
            'Resource/Show',
            $resource->toShow($request)
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function edit(Request $request): Response
    {
        $resource = Resource::resolveFromRequest($request);

        return Inertia::render(
            'Resource/Edit',
            $resource->toEdit($request)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $resource = Resource::resolveFromRequest($request);

        $model = $resource->handleUpdate($request);

        return Redirect::route('root.resource.show', [$resource->getKey(), $model]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $resource = Resource::resolveFromRequest($request);

        $resource->handleDestroy($request);

        return Redirect::route('root.resource.index', $resource->getKey());
    }
}
