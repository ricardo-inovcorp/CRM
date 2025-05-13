<?php

namespace Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;

class FunnelSalesAnalysisTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_calculo_de_metricas_do_funil_de_vendas()
    {
        // Mock do serviço de análise de funil
        $service = Mockery::mock('FunnelAnalysisService');
        
        // Dados do funil de vendas
        $dadosFunil = [
            'lead' => 500,
            'qualificado' => 300,
            'proposta' => 125,
            'negociacao' => 80,
            'fechado' => 40
        ];
        
        // Definir comportamento esperado - cálculo de conversão entre etapas
        $service->shouldReceive('calcularConversaoEntreEtapas')
             ->with($dadosFunil)
             ->once()
             ->andReturnUsing(function($dados) {
                 $etapas = array_keys($dados);
                 $resultado = [];
                 
                 for ($i = 0; $i < count($etapas) - 1; $i++) {
                     $etapaAtual = $etapas[$i];
                     $proximaEtapa = $etapas[$i + 1];
                     $taxaConversao = ($dados[$proximaEtapa] / $dados[$etapaAtual]) * 100;
                     $resultado["{$etapaAtual}_para_{$proximaEtapa}"] = $taxaConversao;
                 }
                 
                 $resultado['global'] = ($dados[$etapas[count($etapas) - 1]] / $dados[$etapas[0]]) * 100;
                 
                 return $resultado;
             });
             
        $taxasConversao = $service->calcularConversaoEntreEtapas($dadosFunil);
        
        // Verificar cálculos de conversão entre etapas:
        // Lead → Qualificado: 300/500 = 60%
        // Qualificado → Proposta: 125/300 = 41.67%
        // Proposta → Negociação: 80/125 = 64%
        // Negociação → Fechado: 40/80 = 50%
        // Global (Lead → Fechado): 40/500 = 8%
        $this->assertEquals(60, $taxasConversao['lead_para_qualificado']);
        $this->assertEqualsWithDelta(41.67, $taxasConversao['qualificado_para_proposta'], 0.01);
        $this->assertEquals(64, $taxasConversao['proposta_para_negociacao']);
        $this->assertEquals(50, $taxasConversao['negociacao_para_fechado']);
        $this->assertEquals(8, $taxasConversao['global']);
    }
    
    public function test_identificacao_de_gargalos_no_funil()
    {
        // Mock do serviço de análise de funil
        $service = Mockery::mock('FunnelAnalysisService');
        
        // Taxas de conversão entre etapas
        $taxasConversao = [
            'lead_para_qualificado' => 60,
            'qualificado_para_proposta' => 20,  // Gargalo!
            'proposta_para_negociacao' => 70,
            'negociacao_para_fechado' => 65
        ];
        
        // Definir comportamento esperado - identificação de gargalos (taxa abaixo de 30%)
        $service->shouldReceive('identificarGargalos')
             ->with($taxasConversao, 30)
             ->once()
             ->andReturnUsing(function($taxas, $limiar) {
                 $gargalos = [];
                 foreach ($taxas as $etapa => $taxa) {
                     if ($taxa < $limiar && $etapa !== 'global') {
                         $gargalos[] = $etapa;
                     }
                 }
                 return $gargalos;
             });
             
        // Verificar que a etapa qualificado_para_proposta foi identificada como gargalo
        $gargalos = $service->identificarGargalos($taxasConversao, 30);
        $this->assertEquals(['qualificado_para_proposta'], $gargalos);
    }
    
    public function test_previsao_de_vendas_baseada_no_funil()
    {
        // Mock do serviço de análise de funil
        $service = Mockery::mock('FunnelAnalysisService');
        
        // Estado atual do funil
        $estadoAtual = [
            'lead' => 800,
            'qualificado' => 400,
            'proposta' => 150,
            'negociacao' => 70,
            'fechado' => 0  // Ainda sem vendas fechadas
        ];
        
        // Taxas históricas de conversão
        $taxasHistoricas = [
            'lead_para_qualificado' => 60,
            'qualificado_para_proposta' => 40,
            'proposta_para_negociacao' => 50,
            'negociacao_para_fechado' => 45
        ];
        
        // Preço médio por venda
        $valorMedio = 15000;
        
        // Definir comportamento esperado - previsão de vendas a partir do funil atual
        $service->shouldReceive('preverVendasEReceita')
             ->with($estadoAtual, $taxasHistoricas, $valorMedio)
             ->once()
             ->andReturnUsing(function($estado, $taxas, $valor) {
                 $previsao = [
                     'leads_para_fechado' => ceil($estado['lead'] * ($taxas['lead_para_qualificado'] / 100) * 
                                              ($taxas['qualificado_para_proposta'] / 100) * 
                                              ($taxas['proposta_para_negociacao'] / 100) * 
                                              ($taxas['negociacao_para_fechado'] / 100)),
                     'qualificados_para_fechado' => ceil($estado['qualificado'] * 
                                                    ($taxas['qualificado_para_proposta'] / 100) * 
                                                    ($taxas['proposta_para_negociacao'] / 100) * 
                                                    ($taxas['negociacao_para_fechado'] / 100)),
                     'propostas_para_fechado' => ceil($estado['proposta'] * 
                                                 ($taxas['proposta_para_negociacao'] / 100) * 
                                                 ($taxas['negociacao_para_fechado'] / 100)),
                     'negociacao_para_fechado' => ceil($estado['negociacao'] * 
                                                  ($taxas['negociacao_para_fechado'] / 100))
                 ];
                 
                 $previsao['total_vendas'] = $previsao['leads_para_fechado'] + 
                                            $previsao['qualificados_para_fechado'] + 
                                            $previsao['propostas_para_fechado'] + 
                                            $previsao['negociacao_para_fechado'];
                                            
                 $previsao['receita_prevista'] = $previsao['total_vendas'] * $valor;
                 
                 return $previsao;
             });
             
        $previsao = $service->preverVendasEReceita($estadoAtual, $taxasHistoricas, $valorMedio);
        
        // Verificar cálculos:
        // Leads → Fechado: 800 * 0.6 * 0.4 * 0.5 * 0.45 ≈ 43.2 → 44 vendas
        // Qualificados → Fechado: 400 * 0.4 * 0.5 * 0.45 ≈ 36 vendas
        // Propostas → Fechado: 150 * 0.5 * 0.45 ≈ 33.75 → 34 vendas
        // Negociação → Fechado: 70 * 0.45 ≈ 31.5 → 32 vendas
        // Total de vendas: 44 + 36 + 34 + 32 = 146 vendas
        // Receita prevista: 146 * 15000 = 2.190.000
        $this->assertEquals(44, $previsao['leads_para_fechado']);
        $this->assertEquals(36, $previsao['qualificados_para_fechado']);
        $this->assertEquals(34, $previsao['propostas_para_fechado']);
        $this->assertEquals(32, $previsao['negociacao_para_fechado']);
        $this->assertEquals(146, $previsao['total_vendas']);
        $this->assertEquals(2190000, $previsao['receita_prevista']);
    }
} 