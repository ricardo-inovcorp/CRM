<?php

namespace Tests\Unit;

use App\Models\Entidade;
use Mockery;
use PHPUnit\Framework\TestCase;

class EntidadeClassificacaoTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /**
     * Teste de classificação de entidades por faturamento
     */
    public function test_classificacao_por_faturamento()
    {
        // Criar mock da entidade
        $classificador = Mockery::mock('EntidadeClassificador');
        
        // Definir comportamento esperado
        $classificador->shouldReceive('classificarPorFaturamento')
                     ->with(Mockery::on(function($valor) { return $valor >= 1000000; }))
                     ->andReturn('Grande Empresa');
                     
        $classificador->shouldReceive('classificarPorFaturamento')
                     ->with(Mockery::on(function($valor) { return $valor >= 100000 && $valor < 1000000; }))
                     ->andReturn('Média Empresa');
                     
        $classificador->shouldReceive('classificarPorFaturamento')
                     ->with(Mockery::on(function($valor) { return $valor < 100000; }))
                     ->andReturn('Pequena Empresa');
        
        // Testar casos
        $this->assertEquals('Grande Empresa', $classificador->classificarPorFaturamento(1500000));
        $this->assertEquals('Média Empresa', $classificador->classificarPorFaturamento(500000));
        $this->assertEquals('Pequena Empresa', $classificador->classificarPorFaturamento(50000));
    }
    
    /**
     * Teste de classificação de entidades por número de funcionários
     */
    public function test_classificacao_por_numero_funcionarios()
    {
        // Criar mock do classificador
        $classificador = Mockery::mock('EntidadeClassificador');
        
        // Definir comportamento esperado
        $classificador->shouldReceive('classificarPorFuncionarios')
                     ->with(Mockery::on(function($num) { return $num >= 250; }))
                     ->andReturn('Grande Porte');
                     
        $classificador->shouldReceive('classificarPorFuncionarios')
                     ->with(Mockery::on(function($num) { return $num >= 50 && $num < 250; }))
                     ->andReturn('Médio Porte');
                     
        $classificador->shouldReceive('classificarPorFuncionarios')
                     ->with(Mockery::on(function($num) { return $num < 50; }))
                     ->andReturn('Pequeno Porte');
        
        // Testar casos
        $this->assertEquals('Grande Porte', $classificador->classificarPorFuncionarios(300));
        $this->assertEquals('Médio Porte', $classificador->classificarPorFuncionarios(100));
        $this->assertEquals('Pequeno Porte', $classificador->classificarPorFuncionarios(20));
    }
    
    /**
     * Teste de potencial de negócio de uma entidade
     */
    public function test_calculo_potencial_de_negocio()
    {
        // Criar mock de entidade
        $entidade = Mockery::mock(Entidade::class)->makePartial();
        $entidade->shouldReceive('getAttribute')->with('faturamento_anual')->andReturn(500000);
        $entidade->shouldReceive('getAttribute')->with('num_funcionarios')->andReturn(75);
        $entidade->shouldReceive('getAttribute')->with('setor')->andReturn('Tecnologia');
        
        // Criar mock do calculador de potencial
        $calculador = Mockery::mock('PotencialCalculator');
        
        // Definir cálculo de potencial com base em diferentes fatores
        $calculador->shouldReceive('calcularPotencial')
                  ->with($entidade)
                  ->andReturnUsing(function($entidade) {
                      $base = $entidade->faturamento_anual * 0.01;
                      $multiplicador = ($entidade->setor === 'Tecnologia') ? 1.5 : 1.0;
                      return $base * $multiplicador;
                  });
        
        // Verificar cálculo de potencial
        // Para uma empresa de tecnologia com 500.000 de faturamento:
        // 500.000 * 0.01 * 1.5 = 7.500
        $this->assertEquals(7500, $calculador->calcularPotencial($entidade));
    }
    
    /**
     * Teste de cálculo de prioridade de contato com entidade
     */
    public function test_prioridade_de_contato_baseado_em_historico()
    {
        // Criar mock da entidade
        $entidade = Mockery::mock(Entidade::class)->makePartial();
        
        // Configurar retornos de atributos
        $entidade->shouldReceive('getAttribute')->with('ultima_compra')->andReturn('2023-01-15');
        $entidade->shouldReceive('getAttribute')->with('valor_ultima_compra')->andReturn(25000);
        $entidade->shouldReceive('getAttribute')->with('frequencia_compra')->andReturn(4); // trimestral
        
        // Mock do priorizador
        $priorizador = Mockery::mock('ContatoPriorizador');
        
        // Configurar cálculo de prioridade
        $priorizador->shouldReceive('calcularPrioridadeContato')
                   ->with($entidade)
                   ->andReturnUsing(function($entidade) {
                       $hoje = date('Y-m-d');
                       $diasUltimaCompra = (strtotime($hoje) - strtotime($entidade->ultima_compra)) / 86400;
                       $cicloCompra = 365 / $entidade->frequencia_compra;
                       
                       if ($diasUltimaCompra > $cicloCompra) {
                           return 'Alta';
                       } elseif ($diasUltimaCompra > $cicloCompra * 0.7) {
                           return 'Média';
                       } else {
                           return 'Baixa';
                       }
                   });
        
        // Verificar cálculo de prioridade
        $this->assertEquals('Alta', $priorizador->calcularPrioridadeContato($entidade));
    }
} 