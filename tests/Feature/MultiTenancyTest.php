<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\Entidade;
use App\Models\Atividade;
use App\Models\Contacto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_only_see_their_tenant_entities()
    {
        // Criar tenants
        $tenant1 = Tenant::factory()->create(['nome' => 'Empresa A']);
        $tenant2 = Tenant::factory()->create(['nome' => 'Empresa B']);
        
        // Criar usuários
        $user1 = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'is_admin' => false
        ]);
        
        $user2 = User::factory()->create([
            'tenant_id' => $tenant2->id,
            'is_admin' => false
        ]);
        
        // Criar entidades para cada tenant
        $entidade1 = Entidade::factory()->create([
            'nome' => 'Entidade Tenant 1',
            'tenant_id' => $tenant1->id
        ]);
        
        $entidade2 = Entidade::factory()->create([
            'nome' => 'Entidade Tenant 2',
            'tenant_id' => $tenant2->id
        ]);
        
        // Teste com usuário do tenant 1
        $response = $this->actingAs($user1)
                        ->get(route('entidades.index'));
        
        $response->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->has('entidades.data', 1)
                    ->where('entidades.data.0.nome', 'Entidade Tenant 1')
                );
        
        // Teste com usuário do tenant 2
        $response = $this->actingAs($user2)
                        ->get(route('entidades.index'));
        
        $response->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->has('entidades.data', 1)
                    ->where('entidades.data.0.nome', 'Entidade Tenant 2')
                );
    }
    
    public function test_user_cannot_access_other_tenants_resources()
    {
        // Criar tenants
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        // Criar usuários
        $user1 = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'is_admin' => false
        ]);
        
        // Criar entidades para tenant 2
        $entidade2 = Entidade::factory()->create([
            'tenant_id' => $tenant2->id
        ]);
        
        // Tentar acessar entidade de outro tenant
        $response = $this->actingAs($user1)
                        ->get(route('entidades.show', $entidade2));
        
        // Deve retornar 404 porque o usuário não tem acesso a este recurso
        $response->assertStatus(404);
    }
    
    public function test_admin_can_see_all_tenants_resources()
    {
        // Criar tenants
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        // Criar admin (sem tenant específico)
        $admin = User::factory()->create([
            'is_admin' => true
        ]);
        
        // Configurar role de admin
        $role = Role::factory()->create(['slug' => 'admin']);
        $admin->roles()->attach($role->id);
        
        // Criar entidades para cada tenant
        $entidade1 = Entidade::factory()->create([
            'nome' => 'Entidade Tenant 1',
            'tenant_id' => $tenant1->id
        ]);
        
        $entidade2 = Entidade::factory()->create([
            'nome' => 'Entidade Tenant 2',
            'tenant_id' => $tenant2->id
        ]);
        
        // Admin deve ver todas as entidades
        $response = $this->actingAs($admin)
                        ->get(route('entidades.index'));
        
        $response->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->has('entidades.data', 2)
                );
    }
    
    public function test_user_cannot_update_tenant_id()
    {
        // Criar tenants
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        
        // Criar usuário
        $user = User::factory()->create([
            'tenant_id' => $tenant1->id,
            'is_admin' => false
        ]);
        
        // Criar entidade
        $entidade = Entidade::factory()->create([
            'nome' => 'Entidade Original',
            'tenant_id' => $tenant1->id
        ]);
        
        // Tentar atualizar o tenant_id
        $response = $this->actingAs($user)
                        ->put(route('entidades.update', $entidade), [
                            'nome' => 'Entidade Atualizada',
                            'tenant_id' => $tenant2->id, // Tentativa de alterar o tenant
                            'email' => 'entidade@exemplo.com',
                            'estado' => 'Ativo'
                        ]);
        
        // Verificar se o tenant_id não foi alterado
        $this->assertDatabaseHas('entidades', [
            'id' => $entidade->id,
            'nome' => 'Entidade Atualizada',
            'tenant_id' => $tenant1->id // Manteve o tenant original
        ]);
    }
} 