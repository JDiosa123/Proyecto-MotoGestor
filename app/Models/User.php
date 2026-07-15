<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'user_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'mecanico_id');
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'creado_por');
    }

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'creado_por');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'activo');
    }
}
