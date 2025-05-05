<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Contacto extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $fillable = [
        'nome',
        'apelido',
        'entidade_id',
        'funcao_id',
        'telefone',
        'telemovel',
        'email',
        'observacoes',
        'estado',
        'tenant_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function entidade(): BelongsTo
    {
        return $this->belongsTo(Entidade::class);
    }

    public function funcao(): BelongsTo
    {
        return $this->belongsTo(Funcao::class);
    }

    public function atividades(): HasMany
    {
        return $this->hasMany(Atividade::class);
    }

    public function nomeCompleto(): string
    {
        return $this->nome . ' ' . $this->apelido;
    }
}
