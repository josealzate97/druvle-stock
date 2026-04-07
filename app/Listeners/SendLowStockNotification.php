<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLowStockNotification implements ShouldQueue
{
    use InteractsWithQueue;

    private const LOW_STOCK_NOTIFICATION_COOLDOWN_MINUTES = 120;
    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(private NotificationService $notificationService)
    {
    }

    public function handle(LowStockDetected $event): void
    {
        if ($this->hasRecentLowStockNotification($event->productId)) {
            return;
        }

        $userIds = User::query()
            ->where('status', User::ACTIVE)
            ->pluck('id')
            ->all();

        if (empty($userIds)) {
            return;
        }

        $this->notificationService->createForUsers($userIds, [
            'type' => Notification::TYPE_STOCK_LOW,
            'title' => 'Stock Bajo: ' . $event->productName,
            'message' => 'El producto "' . $event->productName . '" quedó con ' . $event->currentStock . ' unidades tras la venta ' . $event->sale->code . '. Se requiere reabastecimiento.',
            'priority' => 2,
            'payload' => [
                'sale_id' => $event->sale->id,
                'sale_code' => $event->sale->code,
                'product_id' => $event->productId,
                'product_name' => $event->productName,
                'current_stock' => $event->currentStock,
                'threshold' => $event->threshold,
            ],
            'created_by' => $event->actorUserId,
        ]);
    }

    private function hasRecentLowStockNotification(string $productId): bool
    {
        $cooldownStart = now()->subMinutes(self::LOW_STOCK_NOTIFICATION_COOLDOWN_MINUTES);

        return Notification::query()
            ->where('type', Notification::TYPE_STOCK_LOW)
            ->where('payload->product_id', $productId)
            ->where('created_at', '>=', $cooldownStart)
            ->exists();
    }
}
