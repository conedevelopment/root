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
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class HasOneOrManyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request, Model $model): Response
    {
        $field = $request->resolved();

        return Inertia::render(
            'Relations/Index',
            $field->toIndex($request, $model)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateRequest $request, Model $model): Response
    {
        $field = $request->resolved();

        return Inertia::render(
            'Relations/Form',
            $field->toCreate($request, $model)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request, Model $model): RedirectResponse
    {
        $field = $request->resolved();

        $relation = $field->getRelation($model);

        $related = tap($relation->getRelated(), static function (Model $related) use ($model): void {
            $related->setRelation('parent', $model);
        });

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $related));

        $fields->each->persist($request, $related);

        $relation->save($related);

        $path = sprintf('%s/%s', $request->resolved()->resolveUri($request), $related->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-created', Alert::success(__('The relation has been created!')));
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowRequest $request, Model $model, Model $related): Response
    {
        return Inertia::render(
            'Relations/Show',
            $request->resolved()->toShow($request, $model, $related)
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

        $related->setRelation('parent', $model);

        $fields = $field->resolveFields($request)->available($request, $model, $related);

        $request->validate($fields->mapToValidate($request, $related));

        $fields->each->persist($request, $related);

        $related->save();

        $path = sprintf('%s/%s/edit', $request->resolved()->resolveUri($request), $related->getKey());

        return Redirect::to($path)
                    ->with('alerts.relation-updated', Alert::success(__('The relation has been updated!')));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceRequest $request, Model $model, Model $related): RedirectResponse
    {
        $trashed = class_uses_recursive(SoftDeletes::class) && $related->trashed();

        $trashed ? $related->forceDelete() : $related->delete();

        $path = $request->resolved()->resolveUri($request);

        return Redirect::to($path)
                    ->with('alerts.relation-deleted', Alert::success(__('The relation has been deleted!')));
    }
}
