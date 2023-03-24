<?php

namespace Cone\Root\Http\Controllers;

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
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(AuthorizeResource::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request): Response
    {
        $resource = $request->resource();

        if ($resource->getPolicy($resource->getModel())) {
            $this->authorize('viewAny', $resource->getModel());
        }

        return Inertia::render(
            'Resources/Index',
            $resource->toIndex($request)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateRequest $request): Response
    {
        $resource = $request->resource();

        if ($resource->getPolicy($resource->getModel())) {
            $this->authorize('create', $resource->getModel());
        }

        return Inertia::render(
            'Resources/Form',
            $resource->toCreate($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request): RedirectResponse
    {
        $resource = $request->resource();

        if ($resource->getPolicy($resource->getModel())) {
            $this->authorize('create', $resource->getModel());
        }

        $fields = $resource->resolveFields($request)->available($request);

        $model = $resource->getModelInstance();

        $request->validate($fields->mapToValidate($request, $model));

        $fields->each->persist($request, $model);

        $model->save();

        $resource->created($request, $model);

        return Redirect::to(sprintf('%s/%s', $resource->getUri(), $model->getKey()))
                    ->with('alerts.resource-created', Alert::success(__('The resource has been created!')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(UpdateRequest $request, Model $model): Response
    {
        $resource = $request->resource();

        if ($resource->getPolicy($model)) {
            $this->authorize('update', $model);
        }

        return Inertia::render(
            'Resources/Form',
            $resource->toEdit($request, $model)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Model $model): RedirectResponse
    {
        $resource = $request->resource();

        if ($resource->getPolicy($model)) {
            $this->authorize('update', $model);
        }

        $fields = $resource->resolveFields($request)->available($request, $model);

        $request->validate($fields->mapToValidate($request, $model));

        $fields->each->persist($request, $model);

        $model->save();

        $resource->updated($request, $model);

        return Redirect::to(sprintf('%s/%s', $resource->getUri(), $model->getKey()))
                    ->with('alerts.resource-updated', Alert::success(__('The resource has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceRequest $request, Model $model): RedirectResponse
    {
        $resource = $request->resource();

        $trashed = in_array(SoftDeletes::class, class_uses_recursive($model)) && $model->trashed();

        if ($resource->getPolicy($model)) {
            $this->authorize($trashed ? 'forceDelete' : 'delete', $model);
        }

        $trashed ? $model->forceDelete() : $model->delete();

        $resource->deleted($request, $model);

        return Redirect::to(URL::previousPath() === $resource->getUri() ? URL::previous() : $resource->getUri())
                    ->with('alerts.resource-deleted', Alert::success(__('The resource has been deleted!')));
    }

    /**
     * Restore the specified resource in storage.
     */
    public function restore(ResourceRequest $request, Model $model): RedirectResponse
    {
        $resource = $request->resource();

        if ($resource->getPolicy($model)) {
            $this->authorize('restore', $model);
        }

        $model->restore();

        $resource->restored($request, $model);

        return Redirect::back()
                    ->with('alerts.resource-restored', Alert::success(__('The resource has been restored!')));
    }
}
