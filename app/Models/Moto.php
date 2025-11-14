<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moto extends Model
{
    protected $table = 'motos';
    protected $primaryKey = 'id_moto';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'cliente_id',
        'placa',
        'marca',
        'modelo',
        'cilindraje',
        'color',
    ];

    // Relación al cliente (tu tabla y PK personalizados)
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    // Relación con citas si luego la implementas
    public function citas()
    {
        return $this->hasMany(Cita::class, 'moto_id', 'id_moto');
    }
}
