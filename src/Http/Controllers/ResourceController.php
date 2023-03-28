<?php

namespace Cone\Root\Http\Controllers;

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
    public function index(Request $request): Response
    {
        return Inertia::render(
            'Resources/Index',
            $request->route('rootResource')->toIndex($request)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render(
            'Resources/Form',
            $request->route('rootResource')->toCreate($request)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $model = $request->route('rootResource')->getModelInstance();

        $request->route('rootResource')->toForm($request)->handle($request, $model);

        $request->route('rootResource')->created($request, $model);

        return Redirect::to(sprintf('%s/%s', $request->route('rootResource')->getUri(), $model->getKey()))
                    ->with('alerts.resource-created', Alert::success(__('The resource has been created!')));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(Request $request, Model $model): Response
    {
        return Inertia::render(
            'Resources/Form',
            $request->route('rootResource')->toEdit($request, $model)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $model): RedirectResponse
    {
        $request->route('rootResource')->toForm($request)->handle($request, $model);

        $request->route('rootResource')->updated($request, $model);

        return Redirect::to(sprintf('%s/%s', $request->route('rootResource')->getUri(), $model->getKey()))
                    ->with('alerts.resource-updated', Alert::success(__('The resource has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model): RedirectResponse
    {
        $trashed = in_array(SoftDeletes::class, class_uses_recursive($model)) && $model->trashed();

        $trashed ? $model->forceDelete() : $model->delete();

        $request->route('rootResource')->deleted($request, $model);

        return Redirect::to(URL::previousPath() === $request->route('rootResource')->getUri() ? URL::previous() : $request->route('rootResource')->getUri())
                    ->with('alerts.resource-deleted', Alert::success(__('The resource has been deleted!')));
    }

    /**
     * Restore the specified resource in storage.
     */
    public function restore(Request $request, Model $model): RedirectResponse
    {
        $model->restore();

        $request->route('rootResource')->restored($request, $model);

        return Redirect::back()
                    ->with('alerts.resource-restored', Alert::success(__('The resource has been restored!')));
    }
}
