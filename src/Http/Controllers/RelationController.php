<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Enums\ResourceContext;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class RelationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Model $model): Response
    {
        $relation = $request->route('rootRelation');

        Gate::allowIf($relation->getAbilities($model)['viewAny'] ?? false);

        return Inertia::render(
            'Relations/Index',
            $relation->toIndex($request, $model)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Model $model): Response
    {
        $relation = $request->route('rootRelation');

        Gate::allowIf($relation->getAbilities($model)['create'] ?? false);

        return Inertia::render(
            'Resources/Form',
            $relation->toCreate($request, $model)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Model $model): RedirectResponse
    {
        $relation = $request->route('rootRelation');

        Gate::allowIf($relation->getAbilities($model)['create'] ?? false);

        $item = $relation->newItem($model, $relation->getRelation($model)->getRelated());

        $fields = $relation->resolveFields($request)
                            ->authorized($request, $item->model)
                            ->visible(ResourceContext::Create->value);

        $request->validate($fields->mapToValidate($request, $item->model));

        $fields->each->persist($request, $item->model);

        $item->model->save();

        return Redirect::to($item->resolveUrl($request))
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Model $model, Model $related): Response
    {
        $relation = $request->route('rootRelation');

        Gate::allowIf($relation->newItem($model, $related)->getAbilities()['view'] ?? false);

        return Inertia::render(
            'Resources/Show',
            $relation->toShow($request, $model, $related)
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Model $model, Model $related): Response
    {
        $relation = $request->route('rootRelation');

        Gate::allowIf($relation->newItem($model, $related)->getAbilities()['update'] ?? false);

        return Inertia::render(
            'Resources/Form',
            $related->toEdit($request, $model, $related)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $model, Model $related): RedirectResponse
    {
        $relation = $request->route('rootRelation');

        $item = $relation->newItem($model, $related);

        Gate::allowIf($item->getAbilities()['update'] ?? false);

        $fields = $relation->resolveFields($request)
                            ->authorized($request, $item->model)
                            ->visible(ResourceContext::Update->value);

        $request->validate($fields->mapToValidate($request, $item->model));

        $fields->each->persist($request, $item->model);

        $item->model->save();

        return Redirect::to($item->resolveUrl($request))
                    ->with('alerts.relation-updated', Alert::success(__('The relation has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model, Model $related): RedirectResponse
    {
        $relation = $request->route('rootRelation');

        $item = $relation->newItem($model, $related);

        Gate::allowIf($item->getAbilities()['delete'] ?? false);

        $item->model->delete();

        return Redirect::to($item->resolveUrl($request))
                    ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
