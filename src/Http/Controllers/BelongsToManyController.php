<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BelongsToManyController extends RelationController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Model $model): RedirectResponse
    {
        $field = $request->route('field');

        dd($request->all());

        $field->handleFormRequest($request, $model);

        return Redirect::to('')
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $model, Model $related): RedirectResponse
    {
        $field = $request->route('field');

        $field->handleFormRequest($request, $model);

        return Redirect::back()
                    ->with('alerts.relation-updated', Alert::success(__('The relation has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model, Model $related): RedirectResponse
    {
        //

        return Redirect::to('')
                    ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
