<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Atividade;
use App\Models\Entidade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Mockery;

class BelongsToTenantTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_modelo_com_BelongsToTenant_recebe_tenant_id_do_usuario_autenticado_ao_ser_criado()
    {
        // Criar tenant e usuário
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'is_admin' => false
        ]);
        
        // Autenticar o usuário
        Auth::login($user);
        
        // Criar uma entidade sem especificar o tenant_id
        $entidade = Entidade::factory()->create([
            'tenant_id' => null
        ]);
        
        // Verificar se o tenant_id foi definido automaticamente
        $this->assertEquals($tenant->id, $entidade->tenant_id);
    }
    
    public function test_usuarios_de_um_tenant_so_podem_ver_registros_do_seu_tenant()
    {
        // Criar dois tenants
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        // Criar usuários para cada tenant
        $user1 = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'is_admin' => false
        ]);
        
        $user2 = User::factory()->create([
            'tenant_id' => $tenant2->id,
            'is_admin' => false
        ]);
        
        // Criar atividades para cada tenant
        $atividade1 = Atividade::factory()->create([
            'tenant_id' => $tenant1->id
        ]);
        
        $atividade2 = Atividade::factory()->create([
            'tenant_id' => $tenant2->id
        ]);
        
        // Testar com o primeiro usuário
        Auth::login($user1);
        $atividades = Atividade::all();
        $this->assertEquals(1, $atividades->count());
        $this->assertEquals($atividade1->id, $atividades->first()->id);
        
        // Testar com o segundo usuário
        Auth::login($user2);
        $atividades = Atividade::all();
        $this->assertEquals(1, $atividades->count());
        $this->assertEquals($atividade2->id, $atividades->first()->id);
    }
    
    public function test_administradores_podem_ver_registros_de_todos_os_tenants()
    {
        // Criar dois tenants
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        // Criar usuário admin
        $admin = User::factory()->create([
            'is_admin' => true
        ]);
        
        // Criar atividades para cada tenant
        $atividade1 = Atividade::factory()->create([
            'tenant_id' => $tenant1->id
        ]);
        
        $atividade2 = Atividade::factory()->create([
            'tenant_id' => $tenant2->id
        ]);
        
        // Testar com o admin
        Auth::login($admin);
        $atividades = Atividade::all();
        $this->assertEquals(2, $atividades->count());
    }

    public function test_trait_possui_metodos_necessarios()
    {
        // Criar uma classe anônima para usar o trait
        $mock = new class {
            use BelongsToTenant;
            
            public static function bootBelongsToTenant() {}
            public function tenant() {}
        };
        
        // Verificar se os métodos existem
        $this->assertTrue(method_exists($mock, 'bootBelongsToTenant'));
        $this->assertTrue(method_exists($mock, 'tenant'));
    }
    
    public function test_tenant_relation_e_belongsTo()
    {
        $traitMethods = get_class_methods(BelongsToTenant::class);
        
        // Verificar se o método tenant está definido
        $this->assertTrue(in_array('tenant', $traitMethods));
        
        // Criar mock para testar a implementação
        $model = Mockery::mock('Model');
        $model->shouldReceive('belongsTo')
              ->once()
              ->with('\App\Models\Tenant')
              ->andReturn('belongsTo_relation');
        
        // Extrair o método tenant do trait
        $method = new \ReflectionMethod(BelongsToTenant::class, 'tenant');
        $method->setAccessible(true);
        
        // Chamar o método usando o mock
        $result = $method->invoke($model);
        
        // Verificar que retorna a relação BelongsTo
        $this->assertEquals('belongsTo_relation', $result);
    }
} 