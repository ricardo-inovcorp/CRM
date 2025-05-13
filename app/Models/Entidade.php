<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class Entidade extends Model
{
    use HasFactory, BelongsToTenant;

    const ESTADOS_DISPONIVEIS = ['Ativo', 'Inativo', 'Potencial', 'Cliente', 'Parceiro', 'Fornecedor'];

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
    
    public function logs(): HasMany
    {
        return $this->hasMany(EntidadeLog::class)->orderBy('created_at', 'desc');
    }
    
    public function registrarLog(string $tipo, string $descricao, array $dadosAnteriores = null, array $dadosNovos = null): EntidadeLog
    {
        return EntidadeLog::create([
            'entidade_id' => $this->id,
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'descricao' => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'tenant_id' => $this->tenant_id,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($entidade) {
            // Registrar log antes de excluir
            if (Auth::check()) {
                $entidade->registrarLog(
                    'exclusao',
                    'Entidade excluída por ' . Auth::user()->name,
                    $entidade->toArray(),
                    null
                );
            }
            
            // Primeiro deletar as atividades para evitar problemas de chave estrangeira
            $entidade->atividades()->forceDelete();
            
            // Depois deletar os contatos
            $entidade->contactos()->forceDelete();
        });
    }
}
