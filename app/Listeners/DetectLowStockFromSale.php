<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Events\SaleCompleted;

class DetectLowStockFromSale
{
    private const LOW_STOCK_THRESHOLD = 20;

    public function handle(SaleCompleted $event): void
    {
        foreach ($event->soldProducts as $productData) {
            
            $currentStock = (int) ($productData['current_stock'] ?? 0);

            if ($currentStock > self::LOW_STOCK_THRESHOLD) {
                continue;
            }

            event(new LowStockDetected(
                sale: $event->sale,
                productId: (string) $productData['id'],
                productName: (string) $productData['name'],
                currentStock: $currentStock,
                threshold: self::LOW_STOCK_THRESHOLD,
                actorUserId: $event->actorUserId
            ));
        }
    }
}

