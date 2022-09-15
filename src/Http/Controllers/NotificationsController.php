<?php

declare(strict_types = 1);

namespace Cone\Root\Http\Controllers;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;

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
        $notifications = (Notification::proxy())::rootQuery($request)
                                ->filter($request)
                                ->latest()
                                ->paginate($request->input('per_page'))
                                ->setPath(URL::route('root.api.notifications.index', [], false))
                                ->withQueryString();

        return new JsonResponse(array_merge($notifications->toArray(), [
            'total_unread' => $request->user()->notifications()->unread()->count(),
        ]));
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
        $notification = (Notification::proxy())::rootQuery($request)->findOrFail($id);

        return new JsonResponse($notification);
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
        $notification = (Notification::proxy())::rootQuery($request)->findOrFail($id);

        $notification->markAsRead();

        return new JsonResponse($notification);
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
        $notification = (Notification::proxy())::rootQuery($request)->findOrFail($id);

        $notification->delete();

        return new JsonResponse($notification);
    }
}
