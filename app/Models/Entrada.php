<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Entrada extends Model
{
    protected $table = 'entradas';

    protected $fillable = [
        'id_producto',
        'cantidad',
        'descripcion',
        'creado_por',
        'fecha'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
