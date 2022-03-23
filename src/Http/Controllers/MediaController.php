<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\ResourceRequest;
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
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ResourceRequest $request): JsonResponse
    {
        $field = $request->resolved();

        return new JsonResponse($field->mapItems($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ResourceRequest $request): JsonResponse
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
                ->dispatch($medium, $path, false);

        return new JsonResponse($medium, JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ResourceRequest $request): JsonResponse
    {
        // $medium->delete();

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }
}
