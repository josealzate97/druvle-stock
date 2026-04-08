<?php

namespace App\Listeners;

use App\Events\RefundProcessed;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class SendRefundProcessedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    private const REFUND_NOTIFICATION_COOLDOWN_MINUTES = 10;
    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(private NotificationService $notificationService)
    {
    }

    public function handle(RefundProcessed $event): void
    {
        $lockKey = 'refund_notification_sale_' . $event->sale->id;
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            return;
        }

        try {
            if ($this->hasRecentRefundNotification($event->sale->id)) {
                return;
            }

            $userIds = User::query()
                ->where('status', User::ACTIVE)
                ->pluck('id')
                ->all();

            if (empty($userIds)) {
                return;
            }

            $productsText = collect($event->refundSummary)
                ->map(fn ($item) => ($item['product_name'] ?? 'Producto') . ' (x' . ((int) ($item['quantity'] ?? 0)) . ')')
                ->take(3)
                ->implode(', ');

            $message = 'Se procesó una devolución para la venta ' . $event->sale->code . '.';
            if ($productsText !== '') {
                $message .= ' Productos: ' . $productsText . '.';
            }

            $this->notificationService->createForUsers($userIds, [
                'type' => Notification::TYPE_REFUND,
                'title' => 'Devolución procesada: ' . $event->sale->code,
                'message' => $message,
                'priority' => 2,
                'payload' => [
                    'sale_id' => $event->sale->id,
                    'sale_code' => $event->sale->code,
                    'items' => $event->refundSummary,
                ],
                'created_by' => $event->actorUserId,
            ]);
        } finally {
            optional($lock)->release();
        }
    }

    private function hasRecentRefundNotification(string $saleId): bool
    {
        $cooldownStart = now()->subMinutes(self::REFUND_NOTIFICATION_COOLDOWN_MINUTES);

        return Notification::query()
            ->where('type', Notification::TYPE_REFUND)
            ->where('payload->sale_id', $saleId)
            ->where('created_at', '>=', $cooldownStart)
            ->exists();
    }
}
