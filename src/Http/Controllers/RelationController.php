<?php

declare(strict_types=1);

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
        /** @var \Cone\Root\Fields\Relation $field */
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
        /** @var \Cone\Root\Fields\Relation $field */
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
        /** @var \Cone\Root\Fields\Relation $field */
        $field = $request->route('field');

        $related = $field->getRelation($model)->make()->setRelation('related', $model);

        return $field->handleFormRequest($request, $related);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Model $model, Model $related): Response
    {
        /** @var \Cone\Root\Fields\Relation $field */
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
        /** @var \Cone\Root\Fields\Relation $field */
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
        /** @var \Cone\Root\Fields\Relation $field */
        $field = $request->route('field');

        $related->setRelation('related', $model);

        return $field->handleFormRequest($request, $related);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model, Model $related): RedirectResponse
    {
        /** @var \Cone\Root\Fields\Relation $field */
        $field = $request->route('field');

        $trashed = class_uses_recursive(SoftDeletes::class) && $related->trashed();

        $trashed ? $related->forceDelete() : $related->delete();

        return Redirect::to($field->modelUrl($model))
            ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }

    /**
     * Hydrate the specified resource form.
     */
    public function hydrate(Request $request, Model $model, Model $related): Response
    {
        /** @var \Cone\Root\Fields\Relation $field */
        $field = $request->route('field');

        $field->handleHydrateRequest($request, $model, $related);

        $data = match (true) {
            $model->exists => $field->toEdit($request, $model, $related),
            default => $field->toCreate($request, $model),
        };

        return ResponseFactory::view(
            'root::resources.form-turbo-frame', $data
        );
    }
}
