<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Enums\ResourceContext;
use Cone\Root\Http\Middleware\AuthorizeResource;
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
        $this->middleware(AuthorizeResource::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $resource = $request->route('rootResource');

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
     */
    public function create(Request $request): Response
    {
        $resource = $request->route('rootResource');

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
     */
    public function store(Request $request): RedirectResponse
    {
        $resource = $request->route('rootResource');

        if ($resource->getPolicy()) {
            $this->authorize('create', $resource->getModel());
        }

        $model = $resource->getModelInstance();

        $fields = $resource->resolveFields($request)
                        ->authorized($request, $model)
                        ->visible(ResourceContext::Update->value);

        $request->validate($fields->mapToValidate($request, $model));

        $fields->each->persist($request, $model);

        $model->save();

        $resource->created($request, $model);

        return Redirect::to(sprintf('%s/%s', $resource->getUri(), $model->getKey()))
                    ->with('alerts.resource-created', Alert::success(__('The resource has been created!')));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Model $model): Response
    {
        $resource = $request->route('rootResource');

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
     */
    public function edit(Request $request, Model $model): Response
    {
        $resource = $request->route('rootResource');

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
     */
    public function update(Request $request, Model $model): RedirectResponse
    {
        $resource = $request->route('rootResource');

        if ($resource->getPolicy()) {
            $this->authorize('update', $model);
        }

        $fields = $resource->resolveFields($request)
                        ->authorized($request, $model)
                        ->visible(ResourceContext::Update->value);

        $request->validate($fields->mapToValidate($request, $model));

        $fields->each->persist($request, $model);

        $model->save();

        $resource->updated($request, $model);

        return Redirect::to(sprintf('%s/%s/edit', $resource->getUri(), $model->getKey()))
                    ->with('alerts.resource-updated', Alert::success(__('The resource has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model): RedirectResponse
    {
        $resource = $request->route('rootResource');

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
        $resource = $request->route('rootResource');

        if ($resource->getPolicy()) {
            $this->authorize('restore', $model);
        }

        $model->restore();

        $resource->restored($request, $model);

        return Redirect::back()
                    ->with('alerts.resource-restored', Alert::success(__('The resource has been restored!')));
    }
}
