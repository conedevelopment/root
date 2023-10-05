<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Middleware\AuthorizeResource;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response as ResponseFactory;

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
    public function index(Request $request, Resource $resource): Response
    {
        if ($resource->getPolicy()) {
            $this->authorize('viewAny', $resource->getModel());
        }

        return ResponseFactory::view(
            'root::resources.index',
            $resource->toIndex($request)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Resource $resource): Response
    {
        if ($resource->getPolicy()) {
            $this->authorize('create', $resource->getModel());
        }

        return ResponseFactory::view(
            'root::resources.form',
            $resource->toCreate($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Resource $resource): RedirectResponse
    {
        if ($resource->getPolicy()) {
            $this->authorize('create', $resource->getModel());
        }

        $model = $resource->getModelInstance();

        $resource->handleFormRequest($request, $model);

        return Redirect::to($resource->modelUrl($model))
            ->with('alerts.resource-created', Alert::success(__('The resource has been created!')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Resource $resource, Model $model): Response
    {
        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        return ResponseFactory::view(
            'root::resources.form',
            $resource->toEdit($request, $model)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource, Model $model): RedirectResponse
    {
        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        $resource->toForm($request, $model)->handle($request);

        return Redirect::to($resource->modelUrl($model))
            ->with('alerts.resource-updated', Alert::success(__('The resource has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Resource $resource, Model $model): RedirectResponse
    {
        $trashed = in_array(SoftDeletes::class, class_uses_recursive($model)) && $model->trashed();

        if ($resource->getPolicy()) {
            $this->authorize($trashed ? 'forceDelete' : 'delete', $model);
        }

        $trashed ? $model->forceDelete() : $model->delete();

        return Redirect::route('root.resource.index', $resource->getKey())
            ->with('alerts.resource-deleted', Alert::success(__('The resource has been deleted!')));
    }

    /**
     * Restore the specified resource in storage.
     */
    public function restore(Request $request, Resource $resource, Model $model): RedirectResponse
    {
        if ($resource->getPolicy()) {
            $this->authorize('restore', $model);
        }

        $model->restore();

        return Redirect::back()
            ->with('alerts.resource-restored', Alert::success(__('The resource has been restored!')));
    }
}
