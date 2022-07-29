<?php

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Http\JsonResponse;

class NotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(RootRequest $request): JsonResponse
    {
        $notifications = $request->user()
                                ->notifications()
                                ->filter($request)
                                ->paginate();

        return new JsonResponse($notifications);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(RootRequest $request, string $id): JsonResponse
    {
        //

        return new JsonResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RootRequest $request, string $id): JsonResponse
    {
        //

        return new JsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(RootRequest $request, string $id): JsonResponse
    {
        //

        return new JsonResponse();
    }
}
