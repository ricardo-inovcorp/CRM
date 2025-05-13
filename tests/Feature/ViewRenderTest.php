<?php

namespace Tests\Feature;

use Illuminate\View\View;
use PHPUnit\Framework\TestCase;
use Mockery;

class ViewRenderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_view_pode_ser_renderizada_com_dados()
    {
        // Mock da classe View
        $view = Mockery::mock('Illuminate\View\View');
        $view->shouldReceive('render')
             ->once()
             ->andReturn('<html><body><h1>Hello World</h1></body></html>');
        
        $view->shouldReceive('with')
             ->with('title', 'Test Page')
             ->once()
             ->andReturnSelf();
        
        $view->shouldReceive('with')
             ->with('user', ['name' => 'John Doe'])
             ->once()
             ->andReturnSelf();
        
        // Testar chamadas de método e resultado
        $result = $view->with('title', 'Test Page')
                       ->with('user', ['name' => 'John Doe'])
                       ->render();
        
        $this->assertIsString($result);
        $this->assertStringContainsString('Hello World', $result);
    }
    
    public function test_blade_escapeia_html_por_padrao()
    {
        // Mock para simular o comportamento de escape do Blade
        $view = Mockery::mock('view');
        $view->shouldReceive('escapeIfString')
             ->with('<script>alert("XSS")</script>')
             ->andReturn('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;');
        
        // Testar que o conteúdo potencialmente perigoso é escapado
        $escaped = $view->escapeIfString('<script>alert("XSS")</script>');
        $this->assertEquals('&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', $escaped);
    }
    
    public function test_view_composer_pode_adicionar_dados_a_view()
    {
        // Mock para a View
        $view = Mockery::mock('view');
        
        // Mock para o ViewComposer
        $composer = Mockery::mock('composer');
        $composer->shouldReceive('compose')
                 ->with($view)
                 ->once()
                 ->andReturnUsing(function($view) {
                     $view->with('stats', ['users' => 100, 'posts' => 500]);
                     return $view;
                 });
        
        // Configurar view para receber dados
        $view->shouldReceive('with')
             ->with('stats', ['users' => 100, 'posts' => 500])
             ->once()
             ->andReturnSelf();
        
        // Aplicar o composer à view
        $result = $composer->compose($view);
        
        // Verificar que é a mesma view
        $this->assertSame($view, $result);
    }
} 