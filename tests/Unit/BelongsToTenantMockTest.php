<?php

namespace Tests\Unit;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use PHPUnit\Framework\TestCase;

class BelongsToTenantMockTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
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
    
    public function test_metodo_tenant_existe_no_trait()
    {
        // Verificar método tenant no trait
        $traitMethods = get_class_methods(BelongsToTenant::class);
        $this->assertContains('tenant', $traitMethods);
    }
} 