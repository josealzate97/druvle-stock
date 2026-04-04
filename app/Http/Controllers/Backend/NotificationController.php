<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function list(Request $request)
    {
        $validated = $request->validate([
            'unread' => 'nullable|boolean',
            'type' => 'nullable|string|max:80',
            'priority' => 'nullable|integer|min:1|max:5',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $notifications = $this->notificationService->listForUser($request->user()->id, $validated);

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, string $id)
    {
        $marked = $this->notificationService->markAsRead($request->user()->id, $id);

        if (!$marked) {
            return response()->json([
                'success' => false,
                'message' => 'Notificación no encontrada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída.',
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $updated = $this->notificationService->markAllAsRead($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Notificaciones actualizadas.',
            'updated' => $updated,
        ]);
    }

    public function preferences(Request $request)
    {
        $preferences = $this->notificationService->getPreferences($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|array|min:1',
            'preferences.*.notification_type' => 'required|string|max:80',
            'preferences.*.in_app' => 'nullable|boolean',
            'preferences.*.email' => 'nullable|boolean',
            'preferences.*.push' => 'nullable|boolean',
        ]);

        $preferences = $this->notificationService->updatePreferences(
            $request->user()->id,
            $validated['preferences']
        );

        return response()->json([
            'success' => true,
            'message' => 'Preferencias de notificación actualizadas.',
            'data' => $preferences,
        ]);
    }
}

