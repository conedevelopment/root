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
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request): Response
    {
        $resource = Resource::resolve(
            $request->route()->action['resource']
        );

        if ($resource->getPolicy()) {
            $this->authorize('viewAny', $resource->getModel());
        }

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
        $resource = Resource::resolve(
            $request->route()->action['resource']
        );

        if ($resource->getPolicy()) {
            $this->authorize('create', $resource->getModel());
        }

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
        $resource = Resource::resolve(
            $request->route()->action['resource']
        );

        if ($resource->getPolicy()) {
            $this->authorize('create', $resource->getModel());
        }

        $model = $resource->handleStore($request);

        return Redirect::route("root.{$resource->getKey()}.show", $model);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function show(Request $request, string $id): Response
    {
        $resource = Resource::resolve(
            $request->route()->action['resource']
        );

        $model = $resource->resolveRouteBinding($id);

        if ($resource->getPolicy()) {
            $this->authorize('view', $model);
        }

        return Inertia::render(
            'Resource/Show',
            $resource->toShow($request, $model)
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function edit(Request $request, string $id): Response
    {
        $resource = Resource::resolve(
            $request->route()->action['resource']
        );

        $model = $resource->resolveRouteBinding($id);

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        return Inertia::render(
            'Resource/Edit',
            $resource->toEdit($request, $model)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $resource = Resource::resolve(
            $request->route()->action['resource']
        );

        $model = $resource->resolveRouteBinding($id);

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        $resource->handleStore($request);

        return Redirect::route("root.{$resource->getKey()}.show", $model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $resource = Resource::resolve(
            $request->route()->action['resource']
        );

        $model = $resource->resolveRouteBinding($id);

        if ($resource->getPolicy()) {
            $this->authorize(
                (class_uses_recursive(SoftDeletes::class) && $model->trashed()) ? 'forceDelete' : 'delete',
                $model
            );
        }

        $resource->handleDestroy($request, $model);

        return Redirect::route("root.{$resource->getKey()}.index");
    }
}
