<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si el nombre pluralizado por convención es distinto)
    protected $table = 'sales';
    
    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const ACTIVE = 1;
    const INACTIVE = 2;

    const EFECTIVO = 1;
    const BIZUM = 2;
    const TVP = 3;

    // Definición de los campos que se pueden asignar masivamente
    // Asegúrate de que estos campos existan en tu base de datos
    protected $fillable = [
        'id',
        'client_id',
        'consecutive',
        'code',
        'subtotal',
        'tax',
        'total',
        'currency',
        'status',
        'type_payment',
        'notes',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'tax'          => 'decimal:2',
        'discount'    => 'decimal:2',
        'total'        => 'decimal:2',
        'sale_date'  => 'datetime',
    ];

    /**
     * Cliente asociado (puede ser null)
     */
    public function client()
    {
        return $this->belongsTo(Client::class)->withDefault(); // Retorna cliente vacío si es null
    }

    /**
     * Detalles de la venta
     */
    public function items()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id', 'id');
    }

    /**
     * Devoluciones
     */
    public function returnItems()
    {
        return $this->hasMany(ReturnItems::class, 'sale_id', 'id');
    }
}
