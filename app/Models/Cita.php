<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'cliente_id',
        'moto_id',
        'mecanico_id',
        'fecha',
        'hora',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'string',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function moto()
    {
        return $this->belongsTo(Moto::class, 'moto_id');
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function scopePendiente(Builder $query): Builder
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePorFecha(Builder $query, string $fecha): Builder
    {
        return $query->where('fecha', $fecha);
    }
}

