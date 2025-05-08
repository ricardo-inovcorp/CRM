<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class TipoNegocio extends Model
{
    use HasFactory, UsesTenantConnection;

    protected $table = 'tipos_negocio';

    protected $fillable = [
        'nome',
        'tenant_id',
    ];

    public function negocios(): HasMany
    {
        return $this->hasMany(Negocio::class, 'tipo_id');
    }
} 