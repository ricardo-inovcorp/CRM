<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = Auth::user();
            
            if ($user) {
                if (!$user->is_admin && $user->tenant_id) {
                    Log::debug('Aplicando escopo de tenant: ' . static::class . ' para tenant_id: ' . $user->tenant_id);
                    
                    $builder->where(function($query) use ($user) {
                        $query->where('tenant_id', $user->tenant_id)
                              ->orWhereNull('tenant_id');
                    });
                }
            }
        });
        
        static::creating(function ($model) {
            if (!$model->tenant_id) {
                $user = Auth::user();
                if ($user && $user->tenant_id) {
                    Log::debug('Definindo tenant_id em ' . get_class($model) . ' para: ' . $user->tenant_id);
                    $model->tenant_id = $user->tenant_id;
                }
            }
        });
        
        static::updating(function ($model) {
            $originalTenantId = $model->getOriginal('tenant_id');
            
            if ($originalTenantId && $originalTenantId !== $model->tenant_id) {
                $user = Auth::user();
                if ($user && !$user->is_admin) {
                    Log::debug('Tentativa de alterar tenant_id de ' . $originalTenantId . ' para ' . $model->tenant_id . ' bloqueada');
                    $model->tenant_id = $originalTenantId;
                }
            }
        });
    }
    
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class)->withDefault([
            'name' => 'Sistema',
        ]);
    }
} 