<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Resources\Resource;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Resource $resource): Response
    {
        return Inertia::render(
            'Resources/Index',
            $resource->toIndex($request)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Resource $resource): Response
    {
        return Inertia::render(
            'Resources/Form',
            $resource->toCreate($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Resource $resource): RedirectResponse
    {
        $model = $resource->getModelInstance();

        $resource->toForm($request)->handle($request, $model);

        $resource->created($request, $model);

        return Redirect::to(sprintf('%s/%s', $resource->getUri(), $model->getKey()))
                    ->with('alerts.resource-created', Alert::success(__('The resource has been created!')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Request $request, Resource $resource, Model $model): Response
    {
        return Inertia::render(
            'Resources/Form',
            $resource->toEdit($request, $model)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource, Model $model): RedirectResponse
    {
        $resource->toForm($request)->handle($request, $model);

        $resource->updated($request, $model);

        return Redirect::to(sprintf('%s/%s', $resource->getUri(), $model->getKey()))
                    ->with('alerts.resource-updated', Alert::success(__('The resource has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Resource $resource, Model $model): RedirectResponse
    {
        $trashed = in_array(SoftDeletes::class, class_uses_recursive($model)) && $model->trashed();

        $trashed ? $model->forceDelete() : $model->delete();

        $resource->deleted($request, $model);

        return Redirect::to(URL::previousPath() === $resource->getUri() ? URL::previous() : $resource->getUri())
                    ->with('alerts.resource-deleted', Alert::success(__('The resource has been deleted!')));
    }

    /**
     * Restore the specified resource in storage.
     */
    public function restore(Request $request, Resource $resource, Model $model): RedirectResponse
    {
        $model->restore();

        $resource->restored($request, $model);

        return Redirect::back()
                    ->with('alerts.resource-restored', Alert::success(__('The resource has been restored!')));
    }
}
