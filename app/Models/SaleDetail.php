<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class SaleDetail extends Model {

    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sale_id',
        'product_id',
        'quantity',
        'unitary_price',
        'subtotal',
    ];

    protected $casts = [
        'unitary_price' => 'decimal:2',
        'subtotal'        => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function producto()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
