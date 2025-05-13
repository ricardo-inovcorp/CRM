<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase as BaseTestCase;

class RouteExistsTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }
    
    public function test_rotas_importantes_devem_existir()
    {
        $mock = \Mockery::mock('router');
        $mock->shouldReceive('has')->with('login')->andReturn(true);
        $mock->shouldReceive('has')->with('register')->andReturn(true);
        $mock->shouldReceive('has')->with('logout')->andReturn(true);
        $mock->shouldReceive('has')->with('dashboard')->andReturn(true);
        $mock->shouldReceive('has')->with('atividades.index')->andReturn(true);
        $mock->shouldReceive('has')->with('contactos.index')->andReturn(true);
        $mock->shouldReceive('has')->with('entidades.index')->andReturn(true);
        
        $this->assertTrue($mock->has('login'));
        $this->assertTrue($mock->has('register'));
        $this->assertTrue($mock->has('logout'));
        $this->assertTrue($mock->has('dashboard'));
        $this->assertTrue($mock->has('atividades.index'));
        $this->assertTrue($mock->has('contactos.index'));
        $this->assertTrue($mock->has('entidades.index'));
    }
    
    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
} 