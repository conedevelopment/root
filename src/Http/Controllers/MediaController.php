<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Root;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Model $model = null): JsonResponse
    {
        $field = $request->route('rootField');

        $model ??= Root::instance()->getCurrentResource()->getModelInstance();

        // Gate::allowIf($field->authorized($request, $model));

        $field->form->model(fn (): Model => $model);

        return new JsonResponse($field->paginate($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Model $model = null): JsonResponse
    {
        $field = $request->route('rootField');

        // Gate::allowIf($field->authorized($request, $model));

        $model ??= Root::instance()->getCurrentResource()->getModelInstance();

        $field->form->model(fn (): Model => $model);

        $request->validate(['file' => ['required', 'file']]);

        $file = $request->file('file');

        $medium = $field->store($request, $file);

        return new JsonResponse($medium, JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model = null): JsonResponse
    {
        $field = $request->route('rootField');

        Gate::allowIf($field->authorized($request, $model));

        $model ??= Root::instance()->getCurrentResource()->getModelInstance();

        $field->resolveRelatableQuery($request, $model)
            ->find($request->input('models', []))
            ->each
            ->delete();

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }
}
