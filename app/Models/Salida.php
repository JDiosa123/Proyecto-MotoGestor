<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salidas';

    protected $fillable = [
        'id_producto',
        'cantidad',
        'descripcion',
        'creado_por',
        'fecha',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fecha' => 'date',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function scopeRecientes(Builder $query): Builder
    {
        return $query->orderByDesc('fecha');
    }
}
