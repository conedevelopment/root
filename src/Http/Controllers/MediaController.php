<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $media = Medium::proxy()
                    ->newQuery()
                    ->filter($request)
                    ->latest()
                    ->cursorPaginate($request->input('per_page'))
                    ->withQueryString();

        return new JsonResponse($media);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $file = $request->file('file');

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && ! $request->boolean('is_last')) {
            return new JsonResponse(['uploaded' => true]);
        }

        $medium = (Medium::proxy())::createFrom($path);

        MoveFile::withChain($medium->convertable() ? [new PerformConversions($medium)] : [])
                ->dispatch($medium, $path);

        return new JsonResponse($medium, JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Medium $medium): JsonResponse
    {
        $medium->delete();

        return new JsonResponse(['deleted' => true]);
    }
}
