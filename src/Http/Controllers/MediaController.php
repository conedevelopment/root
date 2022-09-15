<?php

declare(strict_types = 1);

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ResourceRequest $request, Model $model = null): JsonResponse
    {
        $field = $request->resolved();

        $model ??= $request->resource()->getModelInstance();

        return new JsonResponse($field->mapItems($request, $model));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ResourceRequest $request, Model $model = null): JsonResponse
    {
        $request->validate(['file' => ['required', 'file']]);

        $field = $request->resolved();

        $model ??= $request->resource()->getModelInstance();

        $file = $request->file('file');

        $path = Storage::disk('local')->path("root-chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && ! $request->boolean('is_last')) {
            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
        }

        $medium = $field->store($request, $path);

        MoveFile::withChain($medium->convertable() ? [new PerformConversions($medium)] : [])
                ->dispatch($medium, $path, false);

        return new JsonResponse($field->mapOption($request, $model, $medium), JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ResourceRequest $request, Model $model = null): JsonResponse
    {
        $field = $request->resolved();

        $field->resolveQuery($request, $model ?: $request->resource()->getModelInstance())
              ->find($request->input('models', []))
              ->each
              ->delete();

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }
}
