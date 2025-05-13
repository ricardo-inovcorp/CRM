<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class AtividadeLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'atividade_logs';

    protected $fillable = [
        'atividade_id',
        'user_id',
        'tipo', // 'criacao', 'alteracao', 'exclusao'
        'descricao',
        'dados_anteriores',
        'dados_novos',
        'tenant_id',
    ];

    protected $casts = [
        'dados_anteriores' => 'json',
        'dados_novos' => 'json',
        'created_at' => 'datetime',
    ];

    public function atividade(): BelongsTo
    {
        return $this->belongsTo(Atividade::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 