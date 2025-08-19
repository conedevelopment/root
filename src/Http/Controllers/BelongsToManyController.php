<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BelongsToManyController extends RelationController
{
    /**
     * {@inheritdoc}
     */
    public function store(Request $request, Model $model): RedirectResponse
    {
        $field = $request->route('field');

        $relation = $field->getRelation($model);

        $pivot = $relation->newPivot([
            $relation->getForeignPivotKeyName() => $model->getKey(),
        ]);

        $pivot->setRelation('related', $relation->make());

        $pivot->incrementing = true;

        $field->handleFormRequest($request, $pivot);

        return Redirect::to($field->relatedUrl($model, $pivot))
            ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * {@inheritdoc}
     */
    public function update(Request $request, Model $model, Model $related): RedirectResponse
    {
        $field = $request->route('field');

        $relation = $field->getRelation($model);

        $pivot = $related->getRelation($relation->getPivotAccessor());

        $field->handleFormRequest($request, $pivot);

        return Redirect::to($field->relatedUrl($model, $pivot))
            ->with('alerts.relation-updated', Alert::success(__('The relation has been updated!')));
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(Request $request, Model $model, Model $related): RedirectResponse
    {
        $field = $request->route('field');

        $relation = $field->getRelation($model);

        $related->getRelation($relation->getPivotAccessor())->delete();

        return Redirect::to($field->modelUrl($model))
            ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
