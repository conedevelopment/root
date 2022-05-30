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

class BelongsToManyController extends HasManyController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Http\RedirectResponse
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

        $path = sprintf('%s/%s/%s', $request->resolved()->getUri(), $model->getKey(), $pivot->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function show(ShowRequest $request, Model $model, string $id): Response
    {
        $field = $request->resolved();

        $related = $field->getRelatedByPivot($model, $id);

        return Inertia::render(
            'Relations/Show',
            $field->toShow($request, $model, $related)
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function edit(UpdateRequest $request, Model $model, string $id): Response
    {
        $field = $request->resolved();

        $related = $field->getRelatedByPivot($model, $id);

        return Inertia::render(
            'Relations/Form',
            $field->toEdit($request, $model, $related)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Model $model, string $id): RedirectResponse
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $related = $field->getRelatedByPivot($model, $id);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $pivot));

        $fields->each->persist($request, $pivot);

        $path = sprintf('%s/%s/%s/edit', $request->resolved()->getUri(), $model->getKey(), $related->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-updated', Alert::success(__('The relation has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ResourceRequest $request, Model $model, string $id): RedirectResponse
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $related = $field->getRelatedByPivot($model, $id);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $pivot->delete();

        $path = sprintf('%s/%s', $request->resolved()->getUri(), $model->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
