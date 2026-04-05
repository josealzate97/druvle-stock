<?php

namespace App\Events;

use App\Models\Sale;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Sale $sale,
        public string $productId,
        public string $productName,
        public int $currentStock,
        public int $threshold,
        public ?string $actorUserId = null
    ) {
    }
}

