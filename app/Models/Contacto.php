<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToTenant;

class Contacto extends Model
{
    use HasFactory, BelongsToTenant;

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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($contacto) {
            // Deletar todas as atividades relacionadas
            $contacto->atividades()->forceDelete();
        });
    }
}
