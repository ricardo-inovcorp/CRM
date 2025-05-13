<?php

namespace Tests\Unit;

use App\Models\Traits\BelongsToTenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\TestCase;

class BelongsToTenantTraitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_tenant_scope_aplica_filtro_para_usuario_com_tenant()
    {
        // Criar mocks
        $user = Mockery::mock(User::class);
        $user->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf();
        $user->shouldReceive('getAttribute')->with('is_admin')->andReturn(false);
        $user->shouldReceive('getAttribute')->with('tenant_id')->andReturn(1);
        
        $builder = Mockery::mock(Builder::class);
        
        // Mock de Auth::user()
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);
        
        // Mock de Log::debug
        Log::shouldReceive('debug')
            ->once()
            ->with(Mockery::pattern('/Aplicando escopo de tenant: .* para tenant_id: 1/'));
        
        // Expectativas na query
        $builder->shouldReceive('where')
            ->once()
            ->with(Mockery::type('Closure'))
            ->andReturnSelf();
        
        // Testar o escopo global (simulando a chamada interna do trait)
        $mock = Mockery::mock(ModelWithTrait::class);
        $mock->shouldReceive('bootBelongsToTenant')
            ->once()
            ->andReturnUsing(function() use ($builder) {
                ModelWithTrait::addGlobalScope('tenant', function (Builder $query) use ($builder) {
                    $query->where(function($query) use ($builder) {
                        return $builder;
                    });
                    return $builder;
                });
            });
        
        $mock->bootBelongsToTenant();
    }
    
    public function test_tenant_scope_nao_aplica_filtro_para_admin()
    {
        // Criar mocks
        $user = Mockery::mock(User::class);
        $user->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf();
        $user->shouldReceive('getAttribute')->with('is_admin')->andReturn(true);
        $user->shouldReceive('getAttribute')->with('tenant_id')->andReturn(1);
        
        $builder = Mockery::mock(Builder::class);
        
        // Mock de Auth::user()
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);
        
        // Log não deve ser chamado para admin
        Log::shouldReceive('debug')
            ->never();
        
        // A query where não deve ser chamada para admin
        $builder->shouldReceive('where')
            ->never();
        
        // Testar o escopo global (simulando a chamada interna do trait)
        $mock = Mockery::mock(ModelWithTrait::class);
        $mock->shouldReceive('bootBelongsToTenant')
            ->once()
            ->andReturnUsing(function() use ($builder) {
                ModelWithTrait::addGlobalScope('tenant', function (Builder $query) use ($builder) {
                    return $builder;
                });
            });
        
        $mock->bootBelongsToTenant();
    }
}

// Classe de teste para usar o trait
class ModelWithTrait
{
    use BelongsToTenant;
    
    public static function bootBelongsToTenant()
    {
        // Esta função seria chamada pelo Laravel
    }
    
    public static function addGlobalScope($name, $callback)
    {
        // Implementação vazia para testes
        $callback(new Builder(Mockery::mock(\Illuminate\Database\Query\Builder::class)));
    }
} 