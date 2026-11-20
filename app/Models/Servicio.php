<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'nombre',
        'tipo',
        'agendable',
        'ocupa_dia_completo',
        'dias_anticipacion_minima',
        'hora_ingreso',
        'activo',
    ];

    protected $casts = [
        'agendable' => 'boolean',
        'ocupa_dia_completo' => 'boolean',
        'dias_anticipacion_minima' => 'integer',
        'activo' => 'boolean',
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'servicio_id');
    }
}
