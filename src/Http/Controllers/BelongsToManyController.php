<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class BelongsToManyController extends HasManyController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\IndexRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request, Model $model): RedirectResponse
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $related = $relation->getRelated();

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $related));

        $fields->each->persist($request, $related);

        $relation->save($related);

        $path = sprintf('%s/%s/%s', $request->resolved()->getUri(), $model->getKey(), $related->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
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

        $related = $field->getRelation($model)->findOrFail($id);

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $related));

        $fields->each->persist($request, $related);

        $related->save();

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

        $related = $field->getRelation($model)->findOrFail($id);

        $related->delete();

        $path = sprintf('%s/%s', $request->resolved()->getUri(), $model->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
