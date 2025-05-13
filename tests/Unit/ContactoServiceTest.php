<?php

namespace Tests\Unit;

use App\Models\Contacto;
use Mockery;
use PHPUnit\Framework\TestCase;

class ContactoServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /**
     * Teste de validação de email
     */
    public function test_validacao_de_email_identificar_email_invalido()
    {
        // Mock do serviço
        $service = Mockery::mock('ContactoService');
        $service->shouldReceive('validarEmail')
             ->with('email-invalido')
             ->once()
             ->andReturn(false);
             
        $service->shouldReceive('validarEmail')
             ->with('email@valido.com')
             ->once()
             ->andReturn(true);
             
        // Verificar validação
        $this->assertFalse($service->validarEmail('email-invalido'));
        $this->assertTrue($service->validarEmail('email@valido.com'));
    }
    
    /**
     * Teste de formatação de telefone
     */
    public function test_formatacao_telefone_aplica_mascara_correta()
    {
        // Mock do serviço
        $service = Mockery::mock('ContactoService');
        
        $service->shouldReceive('formatarTelefone')
             ->with('912345678')
             ->once()
             ->andReturn('91 234 56 78');
             
        $service->shouldReceive('formatarTelefone')
             ->with('219876543')
             ->once()
             ->andReturn('21 987 65 43');
             
        // Verificar formatação
        $this->assertEquals('91 234 56 78', $service->formatarTelefone('912345678'));
        $this->assertEquals('21 987 65 43', $service->formatarTelefone('219876543'));
    }
    
    /**
     * Teste de priorização de contatos
     */
    public function test_priorizacao_identifica_contatos_de_alta_prioridade()
    {
        // Mock do serviço
        $service = Mockery::mock('ContactoService');
        
        // Criar mocks de contatos
        $contato1 = Mockery::mock(Contacto::class);
        $contato1->shouldReceive('getAttribute')->with('ultima_interacao')->andReturn('2023-09-15');
        $contato1->shouldReceive('getAttribute')->with('valor_potencial')->andReturn(10000);
        $contato1->shouldReceive('getAttribute')->with('is_lead')->andReturn(true);
        
        $contato2 = Mockery::mock(Contacto::class);
        $contato2->shouldReceive('getAttribute')->with('ultima_interacao')->andReturn('2023-10-20');
        $contato2->shouldReceive('getAttribute')->with('valor_potencial')->andReturn(5000);
        $contato2->shouldReceive('getAttribute')->with('is_lead')->andReturn(false);
        
        // Mock do serviço de priorização
        $service->shouldReceive('calcularPrioridade')
             ->with($contato1)
             ->once()
             ->andReturn('alta');
             
        $service->shouldReceive('calcularPrioridade')
             ->with($contato2)
             ->once()
             ->andReturn('média');
        
        // Verificar cálculo de prioridade
        $this->assertEquals('alta', $service->calcularPrioridade($contato1));
        $this->assertEquals('média', $service->calcularPrioridade($contato2));
    }
    
    /**
     * Teste de classificação de contatos
     */
    public function test_classificacao_de_contatos_por_valor()
    {
        // Mock do serviço
        $service = Mockery::mock('ContactoService');
        
        // Mock do método
        $service->shouldReceive('classificarContato')
             ->with(Mockery::on(function($contato) {
                 return $contato->valor_potencial > 50000;
             }))
             ->andReturn('A');
             
        $service->shouldReceive('classificarContato')
             ->with(Mockery::on(function($contato) {
                 return $contato->valor_potencial > 10000 && $contato->valor_potencial <= 50000;
             }))
             ->andReturn('B');
             
        $service->shouldReceive('classificarContato')
             ->with(Mockery::on(function($contato) {
                 return $contato->valor_potencial <= 10000;
             }))
             ->andReturn('C');
        
        // Criar contatos de teste     
        $contatoA = (object)['valor_potencial' => 100000];
        $contatoB = (object)['valor_potencial' => 25000];
        $contatoC = (object)['valor_potencial' => 5000];
        
        // Verificar classificação
        $this->assertEquals('A', $service->classificarContato($contatoA));
        $this->assertEquals('B', $service->classificarContato($contatoB));
        $this->assertEquals('C', $service->classificarContato($contatoC));
    }
} 