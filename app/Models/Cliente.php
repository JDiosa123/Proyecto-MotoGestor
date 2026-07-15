<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = ['nombre', 'apellido', 'documento', 'fecha_nacimiento', 'direccion', 'ciudad', 'telefono', 'email'];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function motos()
    {
        return $this->hasMany(Moto::class, 'cliente_id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where('nombre', 'like', "%{$term}%")
            ->orWhere('apellido', 'like', "%{$term}%")
            ->orWhere('documento', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%");
    }

    public function scopeFromCity(Builder $query, string $city): Builder
    {
        return $query->where('ciudad', $city);
    }
}
