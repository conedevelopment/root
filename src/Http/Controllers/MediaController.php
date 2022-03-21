<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
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
        $field = $request->resolved();

        return new JsonResponse($field->mapItems($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RootRequest $request): JsonResponse
    {
        $field = $request->resolved();

        $file = $request->file('file');

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && ! $request->boolean('is_last')) {
            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
        }

        $medium = $field->store($request, $path);

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
