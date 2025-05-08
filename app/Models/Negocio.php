<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Negocio extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'nome',
        'tipo_id',
        'entidade_id',
        'valor',
        'estado',
        'tenant_id',
    ];

    public const ESTADOS = [
        'novo',
        'contactado',
        'negociacao',
        'proposta',
        'ganho',
        'perdido',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoNegocio::class, 'tipo_id');
    }

    public function entidade(): BelongsTo
    {
        return $this->belongsTo(Entidade::class, 'entidade_id');
    }

    public function contactos(): BelongsToMany
    {
        return $this->belongsToMany(Contacto::class, 'negocio_contacto', 'negocio_id', 'contacto_id');
    }
} 