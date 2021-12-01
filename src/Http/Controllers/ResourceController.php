<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
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
     * @return \Inertia\Response
     */
    public function index(IndexRequest $request): Response
    {
        $resource = $request->resource();

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
     * @return \Inertia\Response
     */
    public function create(CreateRequest $request): Response
    {
        $resource = $request->resource();

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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request): RedirectResponse
    {
        $resource = $request->resource();

        if ($resource->getPolicy()) {
            $this->authorize('create', $resource->getModel());
        }

        $model = $resource->handleStore($request);

        return Redirect::route("root.{$resource->getKey()}.show", $model);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function show(ShowRequest $request, string $id): Response
    {
        $resource = $request->resource();

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
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function edit(UpdateRequest $request, string $id): Response
    {
        $resource = $request->resource();

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
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, string $id): RedirectResponse
    {
        $resource = $request->resource();

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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(RootRequest $request, string $id): RedirectResponse
    {
        $resource = $request->resource();

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
