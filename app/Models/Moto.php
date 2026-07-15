<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Moto extends Model
{
    use HasFactory;

    protected $table = 'motos';

    protected $fillable = [
        'cliente_id',
        'placa',
        'marca',
        'modelo',
        'cilindraje',
        'color',
    ];

    protected $casts = [
        'cilindraje' => 'integer',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'moto_id');
    }

    public function scopeMarca(Builder $query, string $marca): Builder
    {
        return $query->where('marca', $marca);
    }

    public function scopeColor(Builder $query, string $color): Builder
    {
        return $query->where('color', $color);
    }
}
