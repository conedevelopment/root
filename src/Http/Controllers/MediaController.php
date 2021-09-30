<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\MediumStoreRequest as StoreRequest;
use Cone\Root\Http\Requests\MediumUpdateRequest as UpdateRequest;
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        File::ensureDirectoryExists(Storage::disk('local')->path('chunks'));
    }

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
     * @param  \Cone\Root\Http\Requests\MediumStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $file = $request->file('file');

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && ! $request->boolean('is_last')) {
            return new JsonResponse(['uploaded' => true]);
        }

        $medium = Medium::proxy()::createFrom($path);

        MoveFile::withChain($medium->convertable() ? [new PerformConversions($medium)] : [])
                ->dispatch($medium, $path);

        return new JsonResponse($medium, JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Medium $medium): JsonResponse
    {
        return new JsonResponse($medium);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\MediumUpdateRequest  $request
     * @param  \Cone\Root\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Medium $medium): JsonResponse
    {
        $medium->update($request->validated());

        return new JsonResponse(['updated' => true]);
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
