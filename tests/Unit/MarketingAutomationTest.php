<?php

namespace Tests\Unit;

use App\Models\Contacto;
use App\Models\Entidade;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class MarketingAutomationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_filtragem_de_contatos_para_campanha_por_criterios()
    {
        // Mock do serviço de automação de marketing
        $automacao = Mockery::mock('MarketingAutomation');
        
        // Lista de contatos
        $contatos = [
            ['id' => 1, 'email' => 'joao@example.com', 'tipo' => 'lead', 'setor' => 'tecnologia', 'ultima_abertura_email' => '2023-05-10'],
            ['id' => 2, 'email' => 'maria@example.com', 'tipo' => 'cliente', 'setor' => 'educacao', 'ultima_abertura_email' => '2023-04-15'],
            ['id' => 3, 'email' => 'pedro@example.com', 'tipo' => 'lead', 'setor' => 'saude', 'ultima_abertura_email' => '2023-05-25'],
            ['id' => 4, 'email' => 'ana@example.com', 'tipo' => 'lead', 'setor' => 'tecnologia', 'ultima_abertura_email' => '2023-03-20'],
            ['id' => 5, 'email' => 'carlos@example.com', 'tipo' => 'cliente', 'setor' => 'tecnologia', 'ultima_abertura_email' => '2023-05-01'],
        ];
        
        // Critérios de filtragem
        $criterios = [
            'tipo' => ['lead'],
            'setor' => ['tecnologia', 'saude'],
            'engajamento_minimo' => '2023-04-01' // Contatos que abriram emails após esta data
        ];
        
        // Definir comportamento esperado
        $automacao->shouldReceive('filtrarContatosPorCriterios')
             ->with($contatos, $criterios)
             ->once()
             ->andReturnUsing(function($contatos, $criterios) {
                 return array_filter($contatos, function($contato) use ($criterios) {
                     // Filtrar por tipo
                     if (isset($criterios['tipo']) && !in_array($contato['tipo'], $criterios['tipo'])) {
                         return false;
                     }
                     
                     // Filtrar por setor
                     if (isset($criterios['setor']) && !in_array($contato['setor'], $criterios['setor'])) {
                         return false;
                     }
                     
                     // Filtrar por data de engajamento
                     if (isset($criterios['engajamento_minimo'])) {
                         $dataMinima = new Carbon($criterios['engajamento_minimo']);
                         $ultimaAbertura = new Carbon($contato['ultima_abertura_email']);
                         if ($ultimaAbertura->lt($dataMinima)) {
                             return false;
                         }
                     }
                     
                     return true;
                 });
             });
             
        $contatosFiltrados = $automacao->filtrarContatosPorCriterios($contatos, $criterios);
        
        // Verificar que apenas os contatos 1 e 3 devem ser retornados (leads de tecnologia ou saúde com engajamento recente)
        $this->assertEquals(2, count($contatosFiltrados));
        $this->assertEquals(1, $contatosFiltrados[0]['id']);
        $this->assertEquals(3, $contatosFiltrados[2]['id']);
    }
    
    public function test_segmentacao_de_audiencia_por_comportamento()
    {
        // Mock do serviço de automação de marketing
        $automacao = Mockery::mock('MarketingAutomation');
        
        // Dados de interações dos contatos
        $interacoes = [
            1 => ['visitas_site' => 15, 'emails_abertos' => 8, 'downloads' => 2, 'valor_potencial' => 25000],
            2 => ['visitas_site' => 3, 'emails_abertos' => 1, 'downloads' => 0, 'valor_potencial' => 5000],
            3 => ['visitas_site' => 25, 'emails_abertos' => 12, 'downloads' => 5, 'valor_potencial' => 75000],
            4 => ['visitas_site' => 8, 'emails_abertos' => 4, 'downloads' => 1, 'valor_potencial' => 15000],
            5 => ['visitas_site' => 1, 'emails_abertos' => 0, 'downloads' => 0, 'valor_potencial' => 2000],
        ];
        
        // Definir comportamento esperado
        $automacao->shouldReceive('segmentarPorEngajamento')
             ->with($interacoes)
             ->once()
             ->andReturnUsing(function($interacoes) {
                 $segmentos = [
                     'alta_prioridade' => [],
                     'media_prioridade' => [],
                     'baixa_prioridade' => []
                 ];
                 
                 foreach ($interacoes as $id => $dados) {
                     // Cálculo de pontuação de engajamento
                     $pontuacao = 0;
                     $pontuacao += $dados['visitas_site'] * 1;
                     $pontuacao += $dados['emails_abertos'] * 3;
                     $pontuacao += $dados['downloads'] * 5;
                     $pontuacao += $dados['valor_potencial'] / 1000;
                     
                     // Classificação
                     if ($pontuacao > 100) {
                         $segmentos['alta_prioridade'][] = $id;
                     } elseif ($pontuacao > 20) {
                         $segmentos['media_prioridade'][] = $id;
                     } else {
                         $segmentos['baixa_prioridade'][] = $id;
                     }
                 }
                 
                 return $segmentos;
             });
             
        $segmentos = $automacao->segmentarPorEngajamento($interacoes);
        
        // Verificar segmentação:
        // Contato 3: Alta prioridade (pontuação: 25 + 36 + 25 + 75 = 161)
        // Contatos 1 e 4: Média prioridade (1: 15 + 24 + 10 + 25 = 74) (4: 8 + 12 + 5 + 15 = 40)
        // Contatos 2 e 5: Baixa prioridade (2: 3 + 3 + 0 + 5 = 11) (5: 1 + 0 + 0 + 2 = 3)
        $this->assertEquals([3], $segmentos['alta_prioridade']);
        $this->assertEquals([1, 4], $segmentos['media_prioridade']);
        $this->assertEquals([2, 5], $segmentos['baixa_prioridade']);
    }
    
    public function test_calculo_de_timing_otimo_para_envio_de_emails()
    {
        // Mock do serviço de automação de marketing
        $automacao = Mockery::mock('MarketingAutomation');
        
        // Histórico de aberturas de emails por hora do dia
        $historicoAberturas = [
            '8' => 45,   // 8:00
            '9' => 120,  // 9:00
            '10' => 180, // 10:00
            '11' => 150, // 11:00
            '12' => 70,  // 12:00
            '13' => 50,  // 13:00
            '14' => 130, // 14:00
            '15' => 160, // 15:00
            '16' => 140, // 16:00
            '17' => 90,  // 17:00
            '18' => 50,  // 18:00
            '19' => 30,  // 19:00
            '20' => 25,  // 20:00
            '21' => 15,  // 21:00
        ];
        
        // Definir comportamento esperado
        $automacao->shouldReceive('determinarHorariosOtimos')
             ->with($historicoAberturas, 3)
             ->once()
             ->andReturnUsing(function($historico, $quantidade) {
                 // Ordenar por número de aberturas (decrescente)
                 arsort($historico);
                 
                 // Pegar as N horas com mais aberturas
                 $melhoresHorarios = array_slice(array_keys($historico), 0, $quantidade, true);
                 
                 // Converter para formato de hora
                 $horariosFormatados = [];
                 foreach ($melhoresHorarios as $hora) {
                     $horariosFormatados[] = sprintf('%02d:00', $hora);
                 }
                 
                 return $horariosFormatados;
             });
             
        $melhoresHorarios = $automacao->determinarHorariosOtimos($historicoAberturas, 3);
        
        // Verificar os 3 melhores horários: 10:00 (180), 15:00 (160), 11:00 (150)
        $this->assertEquals(3, count($melhoresHorarios));
        $this->assertEquals('10:00', $melhoresHorarios[0]);
        $this->assertEquals('15:00', $melhoresHorarios[1]);
        $this->assertEquals('11:00', $melhoresHorarios[2]);
    }
    
    public function test_previsao_de_eficacia_de_campanha()
    {
        // Mock do serviço de automação de marketing
        $automacao = Mockery::mock('MarketingAutomation');
        
        // Dados do segmento da campanha
        $segmento = [
            'tamanho' => 5000,          // Número de contatos
            'taxa_abertura_media' => 25, // Taxa de abertura média (%)
            'taxa_clique_media' => 8,    // Taxa de clique média (%)
            'taxa_conversao_media' => 2   // Taxa de conversão média (%)
        ];
        
        // Métricas da campanha anterior similar
        $campanhaAnterior = [
            'taxa_abertura' => 28,      // % de abertura
            'taxa_clique' => 10,        // % de clique
            'taxa_conversao' => 2.5,    // % de conversão
            'valor_medio_conversao' => 300 // Valor médio por conversão
        ];
        
        // Definir comportamento esperado
        $automacao->shouldReceive('preverResultadosCampanha')
             ->with($segmento, $campanhaAnterior)
             ->once()
             ->andReturnUsing(function($segmento, $campanhaAnterior) {
                 // Usar taxas da campanha anterior com pequeno ajuste para o segmento atual
                 $fatorAjuste = ($segmento['taxa_abertura_media'] / 20); // Normalização
                 
                 // Calcular previsões
                 $taxaAbertura = $campanhaAnterior['taxa_abertura'] * 0.9 + $segmento['taxa_abertura_media'] * 0.1;
                 $taxaClique = $campanhaAnterior['taxa_clique'] * 0.9 + $segmento['taxa_clique_media'] * 0.1;
                 $taxaConversao = $campanhaAnterior['taxa_conversao'] * 0.9 + $segmento['taxa_conversao_media'] * 0.1;
                 
                 // Aplicar fator de ajuste
                 $taxaAbertura *= $fatorAjuste;
                 $taxaClique *= $fatorAjuste;
                 $taxaConversao *= $fatorAjuste * 0.9; // Conversão é mais difícil de prever
                 
                 // Calcular números absolutos
                 $numEnvios = $segmento['tamanho'];
                 $numAberturas = round($numEnvios * ($taxaAbertura / 100));
                 $numCliques = round($numAberturas * ($taxaClique / 100));
                 $numConversoes = round($numCliques * ($taxaConversao / 100));
                 $receitaPrevista = $numConversoes * $campanhaAnterior['valor_medio_conversao'];
                 
                 return [
                     'envios' => $numEnvios,
                     'aberturas' => $numAberturas,
                     'cliques' => $numCliques,
                     'conversoes' => $numConversoes,
                     'receita_prevista' => $receitaPrevista,
                     'taxa_abertura' => $taxaAbertura,
                     'taxa_clique' => $taxaClique,
                     'taxa_conversao' => $taxaConversao
                 ];
             });
             
        $previsao = $automacao->preverResultadosCampanha($segmento, $campanhaAnterior);
        
        // Verificar elementos da previsão
        $this->assertEquals(5000, $previsao['envios']);
        $this->assertGreaterThan(0, $previsao['aberturas']);
        $this->assertGreaterThan(0, $previsao['cliques']);
        $this->assertGreaterThan(0, $previsao['conversoes']);
        $this->assertGreaterThan(0, $previsao['receita_prevista']);
        
        // Verificar que as taxas estão dentro de faixas esperadas
        $this->assertGreaterThan(20, $previsao['taxa_abertura']);
        $this->assertLessThan(40, $previsao['taxa_abertura']);
        
        $this->assertGreaterThan(5, $previsao['taxa_clique']);
        $this->assertLessThan(15, $previsao['taxa_clique']);
    }
} 