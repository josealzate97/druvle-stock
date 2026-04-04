<?php

namespace App\Jobs;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationDispatcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $userIds;
    public string $type;
    public string $title;
    public string $message;
    public ?array $payload;
    public int $priority;
    public $scheduledAt;
    public $expiresAt;
    public ?string $createdBy;

    /**
     * Create a new job instance.
     */
    public function __construct(
        array $userIds,
        string $type,
        string $title,
        string $message,
        ?array $payload = null,
        int $priority = 1,
        $scheduledAt = null,
        $expiresAt = null,
        ?string $createdBy = null
    ) {
        $this->userIds = $userIds;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->payload = $payload;
        $this->priority = $priority;
        $this->scheduledAt = $scheduledAt;
        $this->expiresAt = $expiresAt;
        $this->createdBy = $createdBy;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        $notificationService->createForUsers($this->userIds, [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'payload' => $this->payload,
            'priority' => $this->priority,
            'scheduled_at' => $this->scheduledAt,
            'expires_at' => $this->expiresAt,
            'created_by' => $this->createdBy,
        ]);
    }
}

