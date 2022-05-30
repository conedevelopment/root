<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Http\Middleware\AuthorizeResource;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
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
            'Resources/Index',
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
            'Resources/Form',
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

        $resource->created($request, $model);

        return Redirect::route(sprintf('root.%s.show', $resource->getKey()), $model->getKey())
                    ->with('alerts.resource-created', Alert::success(__('The resource has been created!')));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Inertia\Response
     */
    public function show(ShowRequest $request, Model $model): Response
    {
        $resource = $request->resource();

        if ($resource->getPolicy()) {
            $this->authorize('view', $model);
        }

        return Inertia::render(
            'Resources/Show',
            $resource->toShow($request, $model)
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
          * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Inertia\Response
     */
    public function edit(UpdateRequest $request, Model $model): Response
    {
        $resource = $request->resource();

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        return Inertia::render(
            'Resources/Form',
            $resource->toEdit($request, $model)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Model $model): RedirectResponse
    {
        $resource = $request->resource();

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        $fields = $resource->resolveFields($request)->available($request, $model);

        $request->validate($fields->mapToValidate($request, $model));

        $fields->each->persist($request, $model);

        $model->save();

        $resource->updated($request, $model);

        return Redirect::route(sprintf('root.%s.edit', $resource->getKey()), $model->getKey())
                    ->with('alerts.resource-updated', Alert::success(__('The resource has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ResourceRequest $request, Model $model): RedirectResponse
    {
        $resource = $request->resource();

        $trashed = class_uses_recursive(SoftDeletes::class) && $model->trashed();

        if ($resource->getPolicy()) {
            $this->authorize($trashed ? 'forceDelete' : 'delete', $model);
        }

        $trashed ? $model->forceDelete() : $model->delete();

        $resource->deleted($request, $model);

        return Redirect::route(sprintf('root.%s.index', $resource->getKey()))
                    ->with('alerts.resource-deleted', Alert::success(__('The resource has been deleted!')));
    }
}
