<?php

namespace Cone\Root\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RelationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Model $model)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Model $model)
    {
        //
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Model $model, Model $related)
    {
        //
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
