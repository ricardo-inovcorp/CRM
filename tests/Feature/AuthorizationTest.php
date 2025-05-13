<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;
    
    protected Tenant $tenant;
    
    public function setUp(): void
    {
        parent::setUp();
        
        // Criar tenant
        $this->tenant = Tenant::factory()->create();
        
        // Criar roles
        Role::factory()->create(['slug' => 'admin', 'nome' => 'Administrador']);
        Role::factory()->create(['slug' => 'manager', 'nome' => 'Gestor']);
        Role::factory()->create(['slug' => 'staff', 'nome' => 'Funcionário']);
    }
    
    public function test_admin_can_access_all_areas()
    {
        // Criar usuário admin
        $admin = User::factory()->create(['is_admin' => true]);
        $adminRole = Role::where('slug', 'admin')->first();
        $admin->roles()->attach($adminRole->id);
        
        // Admin deve poder acessar painel de administração
        $response = $this->actingAs($admin)
                        ->get(route('dashboard'));
        
        $response->assertStatus(200);
        
        // Admin deve poder acessar listagem de usuários
        $response = $this->actingAs($admin)
                        ->get(route('users.index'));
        
        $response->assertStatus(200);
    }
    
    public function test_manager_can_access_limited_areas()
    {
        // Criar usuário manager
        $manager = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_admin' => false
        ]);
        $managerRole = Role::where('slug', 'manager')->first();
        $manager->roles()->attach($managerRole->id);
        
        // Manager deve poder acessar painel
        $response = $this->actingAs($manager)
                        ->get(route('dashboard'));
        
        $response->assertStatus(200);
        
        // Manager deve poder ver relatórios
        $response = $this->actingAs($manager)
                        ->get(route('relatorios.index'));
        
        $response->assertStatus(200);
    }
    
    public function test_staff_has_limited_access()
    {
        // Criar usuário staff
        $staff = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_admin' => false
        ]);
        $staffRole = Role::where('slug', 'staff')->first();
        $staff->roles()->attach($staffRole->id);
        
        // Staff deve poder acessar painel
        $response = $this->actingAs($staff)
                        ->get(route('dashboard'));
        
        $response->assertStatus(200);
        
        // Staff deve poder acessar próprias atividades
        $response = $this->actingAs($staff)
                        ->get(route('atividades.index'));
        
        $response->assertStatus(200);
    }
    
    public function test_guest_cannot_access_authenticated_routes()
    {
        // Usuário não autenticado deve ser redirecionado para login
        $response = $this->get(route('dashboard'));
        
        $response->assertRedirect(route('login'));
        
        $response = $this->get(route('atividades.index'));
        
        $response->assertRedirect(route('login'));
    }
    
    public function test_user_requires_correct_roles()
    {
        // Criar usuário sem roles
        $user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_admin' => false
        ]);
        
        // Verificar métodos auxiliares de roles
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isManager());
        $this->assertFalse($user->isStaff());
        
        // Adicionar role staff
        $staffRole = Role::where('slug', 'staff')->first();
        $user->roles()->attach($staffRole->id);
        
        // Recarregar usuário para atualizar a relação
        $user = User::find($user->id);
        
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isManager());
        $this->assertTrue($user->isStaff());
        
        // Adicionar role manager
        $managerRole = Role::where('slug', 'manager')->first();
        $user->roles()->attach($managerRole->id);
        
        // Recarregar usuário
        $user = User::find($user->id);
        
        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isManager());
        $this->assertTrue($user->isStaff()); // staff também é verdadeiro para managers
    }
} 