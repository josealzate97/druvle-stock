<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string';

    const ACTIVE = 1;
    const INACTIVE = 2;

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    const DELETED_AT = 'delete_date';

    const IS_TAXABLE = true;
    const IS_NOT_TAXABLE = false;

    
    // Definición de los campos que se pueden asignar masivamente
    // Asegúrate de que estos campos existan en tu base de datos
    protected $fillable = [
        'id',
        'name',
        'code',
        'category_id',
        'purchase_price',
        'sale_price',
        'quantity',
        'taxable',
        'tax_id',
        'notes',
        'status'
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

    // Relación con Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}