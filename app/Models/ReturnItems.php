<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ReturnItems extends Model {
    
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    const PROCESSED = 'processed';
    const PENDING = 'pending';
    const CANCELED = 'canceled';

    const RESTOCK = 1;
    const DAMAGED = 2;

    protected $fillable = [
        'id',
        'sale_id',
        'sale_detail_id',
        'user_id',
        'quantity',
        'note',
        'status',
        'reason',
        'created_at'
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

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleDetail()
    {
        return $this->belongsTo(SaleDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}