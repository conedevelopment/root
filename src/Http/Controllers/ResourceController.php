<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Support\Facades\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Cone\Root\Http\Requests\IndexRequest  $request
     * @param  string  $key
     * @return \Inertia\Response
     */
    public function index(IndexRequest $request, string $key): Response
    {
        $resource = Resource::resolve($key);

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
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @param  string  $key
     * @return \Inertia\Response
     */
    public function create(CreateRequest $request, string $key): Response
    {
        $resource = Resource::resolve($key);

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
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @param  string  $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request, string $key): RedirectResponse
    {
        $resource = Resource::resolve($key);

        if ($resource->getPolicy()) {
            $this->authorize('create', $resource->getModel());
        }

        $model = $resource->handleStore($request);

        return Redirect::route('root.resource.show', [$key, $model]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function show(ShowRequest $request, string $key, string $id): Response
    {
        $resource = Resource::resolve($key);

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
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function edit(UpdateRequest $request, string $key, string $id): Response
    {
        $resource = Resource::resolve($key);

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
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, string $key, string $id): RedirectResponse
    {
        $resource = Resource::resolve($key);

        $model = $resource->resolveRouteBinding($id);

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        $resource->handleUpdate($request, $model);

        return Redirect::route('root.resource.show', [$key, $model]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(RootRequest $request, string $key, string $id): RedirectResponse
    {
        $resource = Resource::resolve($key);

        $model = $resource->resolveRouteBinding($id);

        if ($resource->getPolicy()) {
            $this->authorize(
                (class_uses_recursive(SoftDeletes::class) && $model->trashed()) ? 'forceDelete' : 'delete',
                $model
            );
        }

        $resource->handleDestroy($request, $model);

        return Redirect::route('root.resource.index', $key);
    }
}
