<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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

        $data = $field->toIndex($request, $model);

        return ResponseFactory::view(
            $data['template'], $data
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Model $model): Response
    {
        $field = $request->route('field');

        $data = $field->toCreate($request, $model);

        return ResponseFactory::view(
            $data['template'], $data
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Model $model): RedirectResponse
    {
        $field = $request->route('field');

        $related = $field->getRelation($model)->make();

        $field->handleFormRequest($request, $related);

        return Redirect::to($field->relatedUrl($model, $related))
            ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Model $model, Model $related): Response
    {
        $field = $request->route('field');

        $data = $field->toShow($request, $model, $related);

        return ResponseFactory::view(
            $data['template'], $data
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Model $model, Model $related): Response
    {
        $field = $request->route('field');

        $data = $field->toEdit($request, $model, $related);

        return ResponseFactory::view(
            $data['template'], $data
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $model, Model $related): RedirectResponse
    {
        $field = $request->route('field');

        $field->handleFormRequest($request, $related);

        return Redirect::to($field->relatedUrl($model, $related))
            ->with('alerts.relation-updated', Alert::success(__('The relation has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model, Model $related): RedirectResponse
    {
        $field = $request->route('field');

        $trashed = class_uses_recursive(SoftDeletes::class) && $related->trashed();

        $trashed ? $related->forceDelete() : $related->delete();

        return Redirect::to($field->modelUrl($model))
            ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
