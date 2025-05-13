<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class Contacto extends Model
{
    use HasFactory, BelongsToTenant;

    const ESTADOS_DISPONIVEIS = ['Ativo', 'Inativo', 'Potencial', 'Bloqueado'];

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

    public function logs(): HasMany
    {
        return $this->hasMany(ContactoLog::class)->orderBy('created_at', 'desc');
    }
    
    public function registrarLog(string $tipo, string $descricao, array $dadosAnteriores = null, array $dadosNovos = null): ContactoLog
    {
        return ContactoLog::create([
            'contacto_id' => $this->id,
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'descricao' => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'tenant_id' => $this->tenant_id,
        ]);
    }

    public function negocios(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Negocio::class, 'negocio_contacto', 'contacto_id', 'negocio_id');
    }

    public function nomeCompleto(): string
    {
        return $this->nome . ' ' . $this->apelido;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($contacto) {
            // Registrar log antes de excluir
            if (Auth::check()) {
                $contacto->registrarLog(
                    'exclusao',
                    'Contacto excluído por ' . Auth::user()->name,
                    $contacto->toArray(),
                    null
                );
            }
            
            // Deletar todas as atividades relacionadas
            $contacto->atividades()->forceDelete();
        });
    }
}
