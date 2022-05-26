<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Support\Alert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BelongsToManyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRequest $request, Model $model)
    {
        $field = $request->resolved();

        return Inertia::render(
            'Relations/Index',
            $field->toIndex($request, $model)
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateRequest $request, Model $model)
    {
        $field = $request->resolved();

        return Inertia::render(
            'Relations/Form',
            $field->toCreate($request, $model)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request, Model $model)
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $related = $relation->getRelated();

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $related));

        $fields->each->persist($request, $related);

        $relation->save($related);

        $path = sprintf('%s/%s', $request->resolved()->getUri(), $model->getKey(), $related->getKey);

        return Redirect::to($path)
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, Model $model, string $id)
    {
        $field = $request->resolved();

        $related = $field->getRelation($model)->findOrFail($id);

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
     * @return \Illuminate\Http\Response
     */
    public function edit(UpdateRequest $request, Model $model, string $id)
    {
        $field = $request->resolved();

        $related = $field->getRelation($model)->findOrFail($id);

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
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Model $model, string $id)
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
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResourceRequest $request, Model $model, string $id)
    {
        $field = $request->resolved();

        $related = $field->getRelation($model)->findOrFail($id);

        $trashed = class_uses_recursive(SoftDeletes::class) && $related->trashed();

        $trashed ? $related->forceDelete() : $related->delete();

        $path = sprintf('%s/%s', $request->resolved()->getUri(), $model->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
