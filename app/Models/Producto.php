<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'categoria',
        'cantidad',
        'precio',
        'descripcion',
        'fecha_registro',
        'user_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
        'fecha_registro' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeCategoria(Builder $query, string $categoria): Builder
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('cantidad', '>', 0);
    }
}
