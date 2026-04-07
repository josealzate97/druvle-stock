<?php

namespace App\Listeners;

use App\Events\RefundProcessed;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRefundProcessedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(private NotificationService $notificationService)
    {
    }

    public function handle(RefundProcessed $event): void
    {
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
    }
}
