<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salidas';

    protected $primaryKey = 'id_salida';

    protected $fillable = [
        'id_producto',
        'cantidad',
        'descripcion',
        'creado_por',
        'fecha',
    ];

    public $timestamps = false; 
    
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
