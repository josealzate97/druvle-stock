<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Traits\BelongsToTenant;

class Settings extends Model {
    
    use HasFactory, BelongsToTenant;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'tenant_id',
        'company_name',
        'nit',
        'phone',
        'address',
        'city',
        'logo',
    ];

    protected static function boot() {

        parent::boot();

        static::creating(function ($model) {

            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

        });

    }
}
