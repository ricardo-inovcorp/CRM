<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Entidade extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'nome',
        'morada',
        'codigo_postal',
        'localidade',
        'pais_id',
        'telefone',
        'email',
        'website',
        'observacoes',
        'estado',
        'tenant_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class);
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class);
    }

    public function atividades(): HasMany
    {
        return $this->hasMany(Atividade::class);
    }
}
