<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Illuminate\Support\Facades\Auth;
use App\Models\Traits\BelongsToTenant;

class Negocio extends Model
{
    use HasFactory, BelongsToTenant;

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

    protected static function booted()
    {
        parent::booted();
        static::addGlobalScope('tenant', function ($query) {
            if (Auth::check()) {
                $query->where('tenant_id', Auth::user()->tenant_id);
            }
        });
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoNegocio::class, 'tipo_id');
    }

    public function entidade(): BelongsTo
    {
        return $this->belongsTo(Entidade::class, 'entidade_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NegocioLog::class)->orderBy('created_at', 'desc');
    }
    
    public function registrarLog(string $tipo, string $descricao, array $dadosAnteriores = null, array $dadosNovos = null): NegocioLog
    {
        return NegocioLog::create([
            'negocio_id' => $this->id,
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'descricao' => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'tenant_id' => $this->tenant_id,
        ]);
    }

    public function contactos(): BelongsToMany
    {
        return $this->belongsToMany(Contacto::class, 'negocio_contacto', 'negocio_id', 'contacto_id')
            ->withGlobalScope('tenant', function ($query) {
                if (Auth::check()) {
                    $query->where('contactos.tenant_id', Auth::user()->tenant_id)
                          ->orWhereNull('contactos.tenant_id');
                }
            });
    }
    
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($negocio) {
            // Registrar log antes de excluir
            if (Auth::check()) {
                $negocio->registrarLog(
                    'exclusao',
                    'Negócio excluído por ' . Auth::user()->name,
                    $negocio->toArray(),
                    null
                );
            }
        });
    }
} 