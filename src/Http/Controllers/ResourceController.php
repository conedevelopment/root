<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Middleware\AuthorizeResource;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
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
        $this->middleware(AuthorizeResource::class);
    }

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

        $fields = $resource->resolveFields($request)->available($request);

        $model = $resource->getModelInstance();

        $request->validate($fields->mapToValidate($request, $model));

        $fields->each->persist($request, $model);

        $model->save();

        return Redirect::route('root.resource.show', [$resource->getKey(), $model]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @return \Inertia\Response
     */
    public function show(ShowRequest $request): Response
    {
        $resource = $request->resource();

        $model = $resource->resolveRouteBinding($request, $request->route('id'));

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
     * @return \Inertia\Response
     */
    public function edit(UpdateRequest $request): Response
    {
        $resource = $request->resource();

        $model = $resource->resolveRouteBinding($request, $request->route('id'));

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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request): RedirectResponse
    {
        $resource = $request->resource();

        $model = $resource->resolveRouteBinding($request, $request->route('id'));

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        $fields = $resource->resolveFields($request)->available($request, $model);

        $request->validate($fields->mapToValidate($request, $model));

        $fields->each->persist($request, $model);

        $model->save();

        return Redirect::route('root.resource.show', [$resource->getKey(), $model]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(RootRequest $request): RedirectResponse
    {
        $resource = $request->resource();

        $model = $resource->resolveRouteBinding($request, $request->route('id'));

        $trashed = class_uses_recursive(SoftDeletes::class) && $model->trashed();

        if ($resource->getPolicy()) {
            $this->authorize($trashed ? 'forceDelete' : 'delete', $model);
        }

        $trashed ? $model->forceDelete() : $model->delete();

        return Redirect::route('root.resource.index', $resource->getKey());
    }
}
