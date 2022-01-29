<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(RootRequest $request): JsonResponse
    {
        $field = $request->route('resolved');

        $model = $request->resource()->getModelInstance();

        $media = $field->resolveQuery($request, $model)
                    ->filter($request)
                    ->latest()
                    ->cursorPaginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(static function (Model $related) use ($request, $model, $field): array {
                        return $field->mapOption($request, $model, $related);
                    });

        return new JsonResponse($media);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RootRequest $request): JsonResponse
    {
        $field = $request->route('resolved');

        $file = $request->file('file');

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && ! $request->boolean('is_last')) {
            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
        }

        $medium = $request->user()->uploads()->save(
            (Medium::proxy())::makeFrom($path)
        );

        MoveFile::withChain($medium->convertable() ? [new PerformConversions($medium)] : [])
                ->dispatch($medium, $path);

        return new JsonResponse($medium, JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(RootRequest $request): JsonResponse
    {
        // $medium->delete();

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }
}
