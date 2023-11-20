<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response as ResponseFactory;

class RelationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Model $model): Response
    {
        $field = $request->route('field');

        // Gate::allowIf($field->authorized($request, $model));

        return ResponseFactory::view(
            'root::resources.relation',
            $field->toIndex($request, $model)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Model $model): Response
    {
        $field = $request->route('field');

        return ResponseFactory::view(
            'root::resources.form',
            $field->toCreate($request, $model)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Model $model): RedirectResponse
    {
        //

        return Redirect::to('')
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Model $model, Model $related): Response
    {
        $field = $request->route('field');

        return ResponseFactory::view(
            'root::resources.show',
            $field->toShow($request, $model, $related)
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Model $model, Model $related): Response
    {
        $field = $request->route('field');

        return ResponseFactory::view(
            'root::resources.form',
            $field->toEdit($request, $model, $related)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $model, Model $related): RedirectResponse
    {
        //

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
