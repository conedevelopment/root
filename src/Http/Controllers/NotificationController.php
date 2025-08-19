<?php

declare(strict_types=1);

namespace Cone\Root\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->rootNotifications()
            ->paginate($request->input('per_page'))
            ->withQueryString();

        return new JsonResponse(array_merge($notifications->toArray(), [
            'total_unread' => $request->user()->rootNotifications()->unread()->count(),
        ]));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->rootNotifications()->findOrFail($id);

        return new JsonResponse($notification);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->rootNotifications()->findOrFail($id);

        $notification->markAsRead();

        return new JsonResponse($notification);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()->rootNotifications()->findOrFail($id);

        $notification->delete();

        return new JsonResponse(['deleted' => true]);
    }
}
