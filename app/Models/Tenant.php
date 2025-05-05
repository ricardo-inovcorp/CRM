<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'database',
        'logo',
        'email',
        'telefone',
        'active',
        'trial_ends_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    public function entidades(): HasMany
    {
        return $this->hasMany(Entidade::class);
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class);
    }

    public function atividades(): HasMany
    {
        return $this->hasMany(Atividade::class);
    }

    public function paises(): HasMany
    {
        return $this->hasMany(Pais::class);
    }

    public function funcoes(): HasMany
    {
        return $this->hasMany(Funcao::class);
    }

    public function tiposAtividade(): HasMany
    {
        return $this->hasMany(TipoAtividade::class);
    }
}
