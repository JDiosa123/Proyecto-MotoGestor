<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

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
        'servicio',
        'motivo_cancelacion',
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
        return $query->where('estado', 'solicitada');
    }

    public function scopePorFecha(Builder $query, string $fecha): Builder
    {
        return $query->where('fecha', $fecha);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            User::ROLE_ADMIN, User::ROLE_ASSISTANT => $query,
            User::ROLE_MECHANIC => $query->where('mecanico_id', $user->id),
            User::ROLE_CLIENT => $user->cliente
                ? $query->where('cliente_id', $user->cliente->id)
                : $query->whereRaw('1 = 0'),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
