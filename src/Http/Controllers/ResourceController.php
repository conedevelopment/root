<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Middleware\AuthorizeResource;
use Cone\Root\Root;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Illuminate\Support\Facades\URL;

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
    public function index(Request $request): Response
    {
        $resource = Root::instance()->getCurrentResource();

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
    public function create(Request $request): Response
    {
        $resource = Root::instance()->getCurrentResource();

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
    public function store(Request $request): RedirectResponse
    {
        $resource = Root::instance()->getCurrentResource();

        if ($resource->getPolicy()) {
            $this->authorize('create', $resource->getModel());
        }

        $model = $resource->getModelInstance();

        $resource->form($request)
            ->model(fn (): Model => $model)
            ->handle($request);

        $resource->created($request, $model);

        return Redirect::to(sprintf('%s/%s', $resource->getUri(), $model->getRouteKey()))
            ->with('alerts.resource-created', Alert::success(__('The resource has been created!')));
    }

    /**
     * Show the form for ediging the specified resource.
     */
    public function edit(Request $request, Model $model): Response
    {
        $resource = Root::instance()->getCurrentResource();

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        return ResponseFactory::view(
            'root::resources.form',
            $resource->toShow($request, $model)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $model): RedirectResponse
    {
        $resource = Root::instance()->getCurrentResource();

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        $resource->form($request)
            ->model(fn (): Model => $model)
            ->handle($request);

        $resource->updated($request, $model);

        return Redirect::to(sprintf('%s/%s/edit', $resource->getUri(), $model->getRouteKey()))
            ->with('alerts.resource-updated', Alert::success(__('The resource has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model): RedirectResponse
    {
        $resource = Root::instance()->getCurrentResource();

        $trashed = in_array(SoftDeletes::class, class_uses_recursive($model)) && $model->trashed();

        if ($resource->getPolicy()) {
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
    public function restore(Request $request, Model $model): RedirectResponse
    {
        $resource = Root::instance()->getCurrentResource();

        if ($resource->getPolicy()) {
            $this->authorize('restore', $model);
        }

        $model->restore();

        $resource->restored($request, $model);

        return Redirect::back()
            ->with('alerts.resource-restored', Alert::success(__('The resource has been restored!')));
    }
}
