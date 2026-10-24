<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_ASSISTANT = 'asistente';
    public const ROLE_STOREKEEPER = 'almacenista';
    public const ROLE_MECHANIC = 'mecanico';
    public const ROLE_CLIENT = 'cliente';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_ASSISTANT,
        self::ROLE_STOREKEEPER,
        self::ROLE_MECHANIC,
        self::ROLE_CLIENT,
    ];

    public const ROLE_LABELS = [
        self::ROLE_ADMIN => 'Administrador',
        self::ROLE_ASSISTANT => 'Asistente',
        self::ROLE_STOREKEEPER => 'Almacenista',
        self::ROLE_MECHANIC => 'Mecánico',
        self::ROLE_CLIENT => 'Cliente',
    ];

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

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return $this->role === self::ROLE_ADMIN || in_array($this->role, $roles, true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'activo');
    }
}
