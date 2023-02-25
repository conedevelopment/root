<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class BelongsToManyController extends HasOneOrManyController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request, Model $model): RedirectResponse
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $related = $relation->getRelated();

        $pivot = $relation->newPivot([$relation->getForeignPivotKeyName() => $model->getKey()]);

        $pivot->setRelation('related', $related);

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $pivot));

        $fields->each->persist($request, $pivot);

        $pivot->save();

        $path = sprintf('%s/%s', $request->resolved()->resolveUri($request), $pivot->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowRequest $request, Model $model, Model $related): Response
    {
        $field = $request->resolved();

        return Inertia::render(
            'Relations/Show',
            $field->toShow($request, $model, $related)
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UpdateRequest $request, Model $model, Model $related): Response
    {
        $field = $request->resolved();

        return Inertia::render(
            'Relations/Form',
            $field->toEdit($request, $model, $related)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Model $model, Model $related): RedirectResponse
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $pivot));

        $fields->each->persist($request, $pivot);

        $path = sprintf('%s/%s/edit', $request->resolved()->resolveUri($request), $pivot->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-updated', Alert::success(__('The relation has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceRequest $request, Model $model, Model $related): RedirectResponse
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $pivot->delete();

        $path = $request->resolved()->resolveUri($request);

        return Redirect::to($path)
                    ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
