<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\UserNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class NotificationService
{
    public function createNotification(array $data, ?string $createdBy = null): Notification
    {
        return Notification::query()->create([
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'payload' => $data['payload'] ?? null,
            'priority' => (int) ($data['priority'] ?? 1),
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'created_by' => $createdBy,
        ]);
    }

    public function updateNotification(string $id, array $data): ?Notification
    {
        $notification = Notification::query()->find($id);

        if (!$notification) {
            return null;
        }

        $notification->update([
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'payload' => $data['payload'] ?? null,
            'priority' => (int) ($data['priority'] ?? 1),
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return $notification->fresh();
    }

    public function deleteNotification(string $id): bool
    {
        $notification = Notification::query()->find($id);

        if (!$notification) {
            return false;
        }

        $notification->delete();

        return true;
    }

    public function listForUser(string $userId, array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 20);
        $perPage = max(1, min(100, $perPage));

        return UserNotification::query()
            ->with('notification')
            ->where('user_id', $userId)
            ->when(array_key_exists('unread', $filters), function ($query) use ($filters) {
                if ((bool) $filters['unread'] === true) {
                    $query->whereNull('read_at');
                }
            })
            ->when(!empty($filters['type']), function ($query) use ($filters) {
                $query->whereHas('notification', function ($notificationQuery) use ($filters) {
                    $notificationQuery->where('type', $filters['type']);
                });
            })
            ->when(isset($filters['priority']), function ($query) use ($filters) {
                $query->whereHas('notification', function ($notificationQuery) use ($filters) {
                    $notificationQuery->where('priority', (int) $filters['priority']);
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function markAsRead(string $userId, string $userNotificationId): bool
    {
        $record = UserNotification::query()
            ->where('id', $userNotificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$record) {
            return false;
        }

        if (!$record->read_at) {
            $record->read_at = Carbon::now();
            $record->save();
        }

        return true;
    }

    public function markAllAsRead(string $userId): int
    {
        return UserNotification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);
    }

    public function getPreferences(string $userId)
    {
        return NotificationPreference::query()
            ->where('user_id', $userId)
            ->orderBy('notification_type')
            ->get();
    }

    public function updatePreferences(string $userId, array $preferences)
    {
        foreach ($preferences as $preference) {
            NotificationPreference::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'notification_type' => $preference['notification_type'],
                ],
                [
                    'in_app' => (bool) ($preference['in_app'] ?? true),
                    'email' => (bool) ($preference['email'] ?? false),
                    'push' => (bool) ($preference['push'] ?? false),
                ]
            );
        }

        return $this->getPreferences($userId);
    }

    public function createForUsers(array $userIds, array $data): Notification
    {
        $notification = Notification::query()->create([
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'payload' => $data['payload'] ?? null,
            'priority' => (int) ($data['priority'] ?? 1),
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'created_by' => $data['created_by'] ?? null,
        ]);

        $uniqueUserIds = collect($userIds)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($uniqueUserIds)) {
            return $notification;
        }

        $type = $data['type'];

        $preferences = NotificationPreference::query()
            ->whereIn('user_id', $uniqueUserIds)
            ->where('notification_type', $type)
            ->get()
            ->keyBy('user_id');

        foreach ($uniqueUserIds as $userId) {
            $pref = $preferences->get($userId);
            $enabledInApp = $pref ? (bool) $pref->in_app : true;

            if (!$enabledInApp) {
                continue;
            }

            UserNotification::query()->create([
                'notification_id' => $notification->id,
                'user_id' => $userId,
                'delivered_at' => Carbon::now(),
                'read_at' => null,
                'archived_at' => null,
                'is_starred' => false,
            ]);
        }

        return $notification;
    }
}
