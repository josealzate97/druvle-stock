<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToTenant;

class ProductSize extends Model
{
    use HasFactory, BelongsToTenant;

    const ACTIVE = 1;
    const INACTIVE = 2;

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tenant_id',
        'product_id',
        'name',
        'price',
        'quantity',
        'status',
        'creation_date',
        'update_date',
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
