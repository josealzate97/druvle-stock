<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const ACTIVE = 1;
    const INACTIVE = 2;

    protected $fillable = [
        'id',
        'status',
        'name',
        'email',
        'phone',
        'address',
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

    /**
     * Ventas realizadas por este cliente
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
