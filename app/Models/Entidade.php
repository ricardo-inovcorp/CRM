<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToTenant;

class Entidade extends Model
{
    use HasFactory, BelongsToTenant;

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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($entidade) {
            // Primeiro deletar as atividades para evitar problemas de chave estrangeira
            $entidade->atividades()->forceDelete();
            
            // Depois deletar os contatos
            $entidade->contactos()->forceDelete();
        });
    }
}
