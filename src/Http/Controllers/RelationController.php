<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RelationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Model $model): Response
    {
        // $resource = $request->route('rootResource');

        // if ($resource->getPolicy()) {
        //     $this->authorize('viewAny', $resource->getModel());
        // }

        return Inertia::render(
            'Relations/Index',
            $request->route('rootRelation')->toIndex($request, $model)
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Model $model)
    {
        return Inertia::render(
            'Relations/Form',
            $request->route('rootRelation')->toCreate($request, $model)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Model $model)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Model $model, Model $related)
    {
        return Inertia::render(
            'Relations/Show',
            $request->route('rootRelation')->toShow($request, $model, $related)
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Model $model, Model $related)
    {
        return Inertia::render(
            'Relations/Edit',
            $request->route('rootRelation')->toEdit($request, $model, $related)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Model $model, Model $related)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model, Model $related)
    {
        //
    }
}
