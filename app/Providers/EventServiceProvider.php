<?php

namespace App\Providers;

use App\Events\LowStockDetected;
use App\Events\RefundProcessed;
use App\Events\SaleCompleted;
use App\Listeners\DetectLowStockFromSale;
use App\Listeners\SendLowStockNotification;
use App\Listeners\SendRefundProcessedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        SaleCompleted::class => [
            DetectLowStockFromSale::class,
        ],
        LowStockDetected::class => [
            SendLowStockNotification::class,
        ],
        RefundProcessed::class => [
            SendRefundProcessedNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
