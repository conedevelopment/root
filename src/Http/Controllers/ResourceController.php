<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

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
     * Display a listing of the resource.
     */
    public function index(Request $request, Resource $resource): Response
    {
        $data = $resource->toIndex($request);

        return ResponseFactory::view(
            $data['template'], $data
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Resource $resource): Response
    {
        $data = $resource->toCreate($request);

        return ResponseFactory::view(
            $data['template'], $data
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Resource $resource): RedirectResponse
    {
        $model = $resource->getModelInstance();

        return $resource->handleFormRequest($request, $model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Resource $resource, Model $model): Response
    {
        $data = $resource->toShow($request, $model);

        return ResponseFactory::view(
            $data['template'], $data
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Resource $resource, Model $model): Response
    {
        $data = $resource->toEdit($request, $model);

        return ResponseFactory::view(
            $data['template'], $data
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource, Model $model): RedirectResponse
    {
        return $resource->handleFormRequest($request, $model);
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
        $model->restore();

        return Redirect::back()
            ->with('alerts.resource-restored', Alert::success(__('The resource has been restored!')));
    }

    /**
     * Hydrate the specified resource form.
     */
    public function hydrate(Request $request, Resource $resource, Model $model): Response
    {
        $resource->handleHydrateRequest($request, $model);

        $data = match (true) {
            $model->exists => $resource->toEdit($request, $model),
            default => $resource->toCreate($request),
        };

        return ResponseFactory::view(
            'root::resources.form-turbo-frame', $data
        );
    }
}
