<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    const DELETED_AT = 'delete_date';

    const ACTIVE = 1;
    const INACTIVE = 2;

    protected $fillable = [
        'id',
        'name',
        'abbreviation',
        'icon',
        'color',
        'status',
        'creation_date',
        'update_date',
        'delete_date',
    ];

    protected static function boot() {
        
        parent::boot();

        static::creating(function ($model) {

            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
        });

    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
