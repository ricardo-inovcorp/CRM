<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Funcao extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $table = 'funcoes';

    protected $fillable = [
        'nome',
        'tenant_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class, 'funcao_id');
    }
}
