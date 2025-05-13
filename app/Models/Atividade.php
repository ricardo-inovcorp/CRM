<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;

class Atividade extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'data',
        'hora',
        'duracao',
        'entidade_id',
        'contacto_id',
        'tipo_id',
        'descricao',
        'tenant_id',
    ];

    protected $casts = [
        'data' => 'date:Y-m-d',
        'hora' => 'datetime:H:i',
    ];

    public function entidade(): BelongsTo
    {
        return $this->belongsTo(Entidade::class);
    }

    public function contacto(): BelongsTo
    {
        return $this->belongsTo(Contacto::class);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoAtividade::class, 'tipo_id');
    }

    public function participantes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'atividade_participante')
                    ->withTimestamps();
    }

    public function conhecimento(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'atividade_conhecimento')
                    ->withTimestamps();
    }
    
    public function logs(): HasMany
    {
        return $this->hasMany(AtividadeLog::class)->orderBy('created_at', 'desc');
    }

    public function registrarLog(string $tipo, string $descricao, array $dadosAnteriores = null, array $dadosNovos = null): AtividadeLog
    {
        return AtividadeLog::create([
            'atividade_id' => $this->id,
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'descricao' => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'tenant_id' => $this->tenant_id,
        ]);
    }

    public function duracaoFormatada(): string
    {
        if (!$this->duracao) {
            return '0h';
        }
        
        $horas = floor($this->duracao / 60);
        $minutos = $this->duracao % 60;
        
        if ($horas > 0 && $minutos > 0) {
            return "{$horas}h {$minutos}m";
        } elseif ($horas > 0) {
            return "{$horas}h";
        } else {
            return "{$minutos}m";
        }
    }
}
