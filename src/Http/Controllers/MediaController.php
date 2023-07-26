<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Model $model = null): JsonResponse
    {
        $field = $request->route('rootField');

        Gate::allowIf($field->authorized($request, $model));

        $model ??= $request->route('rootResource')->getModelInstance();

        return new JsonResponse($field->mapItems($request, $model));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Model $model = null): JsonResponse
    {
        $field = $request->route('rootField');

        Gate::allowIf($field->authorized($request, $model));

        $request->validate(['file' => ['required', 'file']]);

        $model ??= $request->route('rootResource')->getModelInstance();

        $file = $request->file('file');

        $path = Storage::disk('local')->path("root-uploads/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->header('X-Chunk-Index') !== $request->header('X-Chunk-Total')) {
            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
        }

        $medium = $field->store($request, $path);

        MoveFile::withChain($medium->convertible() ? [new PerformConversions($medium)] : [])
            ->dispatch($medium, $path, false);

        return new JsonResponse($field->mapOption($request, $model, $medium), JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Model $model = null): JsonResponse
    {
        $field = $request->route('rootField');

        Gate::allowIf($field->authorized($request, $model));

        $field->resolveRelatableQuery($request, $model ?: $request->route('rootResource')->getModelInstance())
            ->find($request->input('models', []))
            ->each
            ->delete();

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }
}
