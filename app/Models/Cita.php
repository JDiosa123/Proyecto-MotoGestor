<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';
    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'cliente_id',
        'moto_id',
        'mecanico_id',
        'fecha',
        'hora',
        'estado'
    ];

    // Relaciones
    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function moto() {
        return $this->belongsTo(Moto::class, 'moto_id');
    }

    public function mecanico() {
        return $this->belongsTo(User::class, 'mecanico_id'); // assuming users table
    }
}

