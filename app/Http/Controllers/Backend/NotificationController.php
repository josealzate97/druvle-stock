<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    public function create(Request $request)
    {
        $validated = $this->validateNotificationData($request);

        $notification = $this->notificationService->createNotification($validated, $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Notificación creada correctamente.',
            'data' => $notification,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $this->validateNotificationData($request);

        $notification = $this->notificationService->updateNotification($id, $validated);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notificación no encontrada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notificación actualizada correctamente.',
            'data' => $notification,
        ]);
    }

    public function delete(string $id)
    {
        $deleted = $this->notificationService->deleteNotification($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Notificación no encontrada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notificación eliminada correctamente.',
        ]);
    }

    private function normalizePayload($payload): ?array
    {
        if ($payload === null || $payload === '') {
            return null;
        }

        if (is_array($payload)) {
            return $payload;
        }

        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        return null;
    }

    private function validateNotificationData(Request $request): array
    {
        $validated = $request->validate([
            'type' => 'required|string|max:80',
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
            'priority' => 'nullable|integer|min:1|max:5',
            'payload' => 'nullable',
            'scheduled_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:scheduled_at',
        ]);

        if (is_string($validated['payload'] ?? null) && trim($validated['payload']) !== '') {
            json_decode($validated['payload'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    'payload' => 'El payload debe ser un JSON válido.',
                ]);
            }
        }

        $validated['payload'] = $this->normalizePayload($validated['payload'] ?? null);

        return $validated;
    }
}
