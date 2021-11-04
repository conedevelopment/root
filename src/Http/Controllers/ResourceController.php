<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Support\Facades\Resource as Registry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Inertia\Response
     */
    public function index(Request $request, string $key): Response
    {
        $resource = Registry::resolve($key);

        return $resource->toIndex($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Inertia\Response
     */
    public function create(Request $request, string $key): Response
    {
        $resource = Registry::resolve($key);

        return $resource->toCreate($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, string $key): RedirectResponse
    {
        $resource = Registry::resolve($key);

        return $resource->handleStore($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function show(Request $request, string $key, string $id): Response
    {
        $resource = Registry::resolve($key);

        return $resource->toShow($request, $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function edit(Request $request, string $key, string $id): Response
    {
        $resource = Registry::resolve($key);

        return $resource->toEdit($request, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $key, string $id): RedirectResponse
    {
        $resource = Registry::resolve($key);

        return $resource->handleUpdate($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, string $key, string $id): RedirectResponse
    {
        $resource = Registry::resolve($key);

        return $resource->handleDestroy($request, $id);
    }
}
