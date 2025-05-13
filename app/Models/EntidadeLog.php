<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class EntidadeLog extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'entidade_logs';

    protected $fillable = [
        'entidade_id',
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

    public function entidade(): BelongsTo
    {
        return $this->belongsTo(Entidade::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 