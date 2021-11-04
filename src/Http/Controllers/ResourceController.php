<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Http\Controllers\Controller;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Facades\Resource as Registry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResourceController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request  $request
     */
    protected Request $request;

    /**
     * The resource instance.
     *
     * @var \Cone\Root\Resources\Resource
     */
    protected Resource $resource;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        try {
            $this->resource = Registry::resolve($request->route('resource'));
        } catch (ResourceResolutionException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }

        $this->request = $request;

        if (! is_null($this->resource->getPolicy())) {
            $this->authorizeResource($this->resource->getModel());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        return Inertia::render(
            'Resource/Index',
            $this->resource->toIndex($this->request)
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        return Inertia::render(
            'Resource/Create',
            $this->resource->toCreate($this->request)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(): RedirectResponse
    {
        $model = $this->resource->handleStore($this->request);

        return Redirect::route('root.resource.show', [$this->resource->getKey(), $model]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Inertia\Response
     */
    public function show(): Response
    {
        return Inertia::render(
            'Resource/Show',
            $this->resource->toShow($this->request, $this->request->route('id'))
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Inertia\Response
     */
    public function edit(): Response
    {
        return Inertia::render(
            'Resource/Edit',
            $this->resource->toEdit($this->request, $this->request->route('id'))
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(): RedirectResponse
    {
        $model = $this->resource->handleUpdate($this->request, $this->request->route('id'));

        return Redirect::route('root.resource.show', [$this->resource->getKey(), $model]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(): RedirectResponse
    {
        $this->resource->handleDestroy($this->request, $this->request->route('id'));

        return Redirect::route('root.resource.index', $this->resource->getKey());
    }
}
