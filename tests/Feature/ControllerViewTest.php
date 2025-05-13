<?php

namespace Tests\Feature;

use App\Http\Controllers\AtividadeController;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Mockery;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase as BaseTestCase;

class ControllerViewTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function createApplication()
    {
        // Este método é necessário para estender TestCase, mas não vamos usá-lo
        return new Application();
    }
    
    public function test_controller_metodos_existem()
    {
        // Verificar se os métodos existem no controller
        $this->assertTrue(method_exists(AtividadeController::class, 'index'));
        $this->assertTrue(method_exists(AtividadeController::class, 'create'));
        $this->assertTrue(method_exists(AtividadeController::class, 'store'));
        $this->assertTrue(method_exists(AtividadeController::class, 'show'));
        $this->assertTrue(method_exists(AtividadeController::class, 'edit'));
        $this->assertTrue(method_exists(AtividadeController::class, 'update'));
        $this->assertTrue(method_exists(AtividadeController::class, 'destroy'));
    }
} 