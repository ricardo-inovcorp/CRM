<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Pais extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $table = 'paises';

    protected $fillable = [
        'nome',
        'codigo',
        'tenant_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function entidades(): HasMany
    {
        return $this->hasMany(Entidade::class, 'pais_id');
    }
}
