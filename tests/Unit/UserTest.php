<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\Atividade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_roles_relationship()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        
        $user->roles()->attach($role->id);
        
        $this->assertEquals(1, $user->roles->count());
        $this->assertEquals($role->id, $user->roles->first()->id);
    }
    
    public function test_user_belongs_to_tenant()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id
        ]);
        
        $this->assertEquals($tenant->id, $user->tenant->id);
    }
    
    public function test_has_role_checks_if_user_has_specified_role()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['slug' => 'staff']);
        
        $user->roles()->attach($role->id);
        
        $this->assertTrue($user->hasRole('staff'));
        $this->assertFalse($user->hasRole('admin'));
    }
    
    public function test_isAdmin_returns_true_only_for_admin_role()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'admin']);
        $staffRole = Role::factory()->create(['slug' => 'staff']);
        
        // Primeiro teste sem role
        $this->assertFalse($user->isAdmin());
        
        // Adiciona role staff
        $user->roles()->attach($staffRole->id);
        $this->assertFalse($user->isAdmin());
        
        // Adiciona role admin
        $user->roles()->attach($adminRole->id);
        $this->assertTrue($user->isAdmin());
    }
    
    public function test_isManager_returns_true_for_manager_and_admin_roles()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'admin']);
        $managerRole = Role::factory()->create(['slug' => 'manager']);
        
        // Primeiro teste sem role
        $this->assertFalse($user->isManager());
        
        // Adiciona role manager
        $user->roles()->attach($managerRole->id);
        $this->assertTrue($user->isManager());
        
        // Limpa roles e adiciona admin
        $user->roles()->detach();
        $user->roles()->attach($adminRole->id);
        $this->assertTrue($user->isManager());
    }
    
    public function test_user_can_have_atividades_as_participante()
    {
        $user = User::factory()->create();
        $atividade = Atividade::factory()->create();
        
        $user->atividadesParticipante()->attach($atividade->id);
        
        $this->assertEquals(1, $user->atividadesParticipante->count());
        $this->assertEquals($atividade->id, $user->atividadesParticipante->first()->id);
    }
    
    public function test_user_can_have_atividades_as_conhecimento()
    {
        $user = User::factory()->create();
        $atividade = Atividade::factory()->create();
        
        $user->atividadesConhecimento()->attach($atividade->id);
        
        $this->assertEquals(1, $user->atividadesConhecimento->count());
        $this->assertEquals($atividade->id, $user->atividadesConhecimento->first()->id);
    }
} 