<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    public const TYPE_STOCK_LOW = 'stock_low';
    public const TYPE_REFUND = 'refund_created';

    public const TYPES = [
        self::TYPE_STOCK_LOW => 'Stock Bajo',
        self::TYPE_REFUND => 'Devoluciones',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'title',
        'message',
        'payload',
        'priority',
        'scheduled_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'payload' => 'array',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class, 'notification_id', 'id');
    }

    public static function allowedTypes(): array
    {
        return array_keys(self::TYPES);
    }

    public static function labelForType(?string $type): string
    {
        if (!$type) {
            return '-';
        }

        return self::TYPES[$type] ?? $type;
    }
}
