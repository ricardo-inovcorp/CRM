<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserRolesTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_hasRole_retorna_true_quando_role_existe()
    {
        // Criar um mock de usuário e uma relação belongsToMany
        $user = $this->createPartialMock(User::class, ['roles']);
        $rolesRelation = Mockery::mock(BelongsToMany::class);
        
        // Configurar o mock
        $user->method('roles')->willReturn($rolesRelation);
        $rolesRelation->shouldReceive('where')->with('slug', 'admin')->andReturnSelf();
        $rolesRelation->shouldReceive('exists')->andReturn(true);
        
        // Verificar resultado
        $this->assertTrue($user->hasRole('admin'));
    }
    
    public function test_hasRole_retorna_false_quando_role_nao_existe()
    {
        // Criar um mock de usuário e uma relação belongsToMany
        $user = $this->createPartialMock(User::class, ['roles']);
        $rolesRelation = Mockery::mock(BelongsToMany::class);
        
        // Configurar o mock
        $user->method('roles')->willReturn($rolesRelation);
        $rolesRelation->shouldReceive('where')->with('slug', 'admin')->andReturnSelf();
        $rolesRelation->shouldReceive('exists')->andReturn(false);
        
        // Verificar resultado
        $this->assertFalse($user->hasRole('admin'));
    }
    
    public function test_isAdmin_depende_de_hasRole_admin()
    {
        // Criar um mock de usuário com método hasRole mockado
        $user = $this->createPartialMock(User::class, ['hasRole']);
        
        // Primeiro teste com hasRole retornando false
        $user->method('hasRole')->with('admin')->willReturn(false);
        $this->assertFalse($user->isAdmin());
        
        // Segundo teste com hasRole retornando true
        $user = $this->createPartialMock(User::class, ['hasRole']);
        $user->method('hasRole')->with('admin')->willReturn(true);
        $this->assertTrue($user->isAdmin());
    }
    
    public function test_isManager_retorna_true_para_manager_e_admin()
    {
        // Teste para manager
        $user = $this->createPartialMock(User::class, ['hasRole', 'isAdmin']);
        $user->method('hasRole')->with('manager')->willReturn(true);
        $user->method('isAdmin')->willReturn(false);
        $this->assertTrue($user->isManager());
        
        // Teste para admin
        $user = $this->createPartialMock(User::class, ['hasRole', 'isAdmin']);
        $user->method('hasRole')->with('manager')->willReturn(false);
        $user->method('isAdmin')->willReturn(true);
        $this->assertTrue($user->isManager());
        
        // Teste para nem manager nem admin
        $user = $this->createPartialMock(User::class, ['hasRole', 'isAdmin']);
        $user->method('hasRole')->with('manager')->willReturn(false);
        $user->method('isAdmin')->willReturn(false);
        $this->assertFalse($user->isManager());
    }
    
    public function test_isStaff_retorna_true_para_staff_manager_e_admin()
    {
        // Teste para staff
        $user = $this->createPartialMock(User::class, ['hasRole', 'isManager']);
        $user->method('hasRole')->with('staff')->willReturn(true);
        $user->method('isManager')->willReturn(false);
        $this->assertTrue($user->isStaff());
        
        // Teste para manager (que não é staff diretamente)
        $user = $this->createPartialMock(User::class, ['hasRole', 'isManager']);
        $user->method('hasRole')->with('staff')->willReturn(false);
        $user->method('isManager')->willReturn(true);
        $this->assertTrue($user->isStaff());
        
        // Teste para nem staff nem manager
        $user = $this->createPartialMock(User::class, ['hasRole', 'isManager']);
        $user->method('hasRole')->with('staff')->willReturn(false);
        $user->method('isManager')->willReturn(false);
        $this->assertFalse($user->isStaff());
    }
} 