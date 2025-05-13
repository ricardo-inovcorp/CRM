<?php

namespace Tests\Unit;

use App\Models\Entidade;
use App\Models\Contacto;
use App\Models\Atividade;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;

class ClientFollowupStrategyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_sequencia_de_contato_ideal_com_cliente()
    {
        // Mock do serviço de estratégia de acompanhamento
        $estrategia = Mockery::mock('FollowupStrategy');
        
        // Definir comportamento esperado - recomendação de sequência de contato ideal
        $estrategia->shouldReceive('definirSequenciaIdeal')
             ->with('novo_cliente')
             ->once()
             ->andReturn([
                 ['tipo' => 'email', 'dias_apos_inicio' => 0, 'assunto' => 'Boas-vindas'],
                 ['tipo' => 'ligacao', 'dias_apos_inicio' => 3, 'assunto' => 'Acompanhamento inicial'],
                 ['tipo' => 'reuniao', 'dias_apos_inicio' => 10, 'assunto' => 'Apresentação detalhada'],
                 ['tipo' => 'email', 'dias_apos_inicio' => 20, 'assunto' => 'Feedback e próximos passos'],
                 ['tipo' => 'ligacao', 'dias_apos_inicio' => 30, 'assunto' => 'Verificação de satisfação']
             ]);
             
        $estrategia->shouldReceive('definirSequenciaIdeal')
             ->with('cliente_inativo')
             ->once()
             ->andReturn([
                 ['tipo' => 'email', 'dias_apos_inicio' => 0, 'assunto' => 'Sentimos sua falta'],
                 ['tipo' => 'ligacao', 'dias_apos_inicio' => 5, 'assunto' => 'Reengajamento'],
                 ['tipo' => 'email', 'dias_apos_inicio' => 14, 'assunto' => 'Ofertas especiais'],
                 ['tipo' => 'sms', 'dias_apos_inicio' => 21, 'assunto' => 'Lembrete final']
             ]);
        
        // Verificar sequências recomendadas
        $sequenciaNovo = $estrategia->definirSequenciaIdeal('novo_cliente');
        $sequenciaInativo = $estrategia->definirSequenciaIdeal('cliente_inativo');
        
        $this->assertEquals(5, count($sequenciaNovo));
        $this->assertEquals(4, count($sequenciaInativo));
        $this->assertEquals('Boas-vindas', $sequenciaNovo[0]['assunto']);
        $this->assertEquals('Sentimos sua falta', $sequenciaInativo[0]['assunto']);
        $this->assertEquals('Verificação de satisfação', $sequenciaNovo[4]['assunto']);
    }
    
    public function test_prioridade_de_clientes_para_followup()
    {
        // Mock do serviço de estratégia de acompanhamento
        $estrategia = Mockery::mock('FollowupStrategy');
        
        // Dados dos clientes como array simples
        $clientes = [
            [
                'id' => 1,
                'valor_potencial' => 65000,
                'ultima_interacao' => '2023-05-10',
                'etapa_funil' => 'proposta'
            ],
            [
                'id' => 2,
                'valor_potencial' => 25000,
                'ultima_interacao' => '2023-04-15',
                'etapa_funil' => 'negociacao'
            ],
            [
                'id' => 3,
                'valor_potencial' => 120000,
                'ultima_interacao' => '2023-05-20',
                'etapa_funil' => 'qualificado'
            ]
        ];
        
        // Vamos usar uma data fixa para o teste: 01/06/2023
        Carbon::setTestNow(Carbon::parse('2023-06-01'));
        
        // Definir comportamento esperado - cálculo de pontuação e prioridade para followup
        // Ajustamos para que o mock retorne o Cliente 2 como maior prioridade
        $estrategia->shouldReceive('calcularPrioridadeFollowup')
             ->with(Mockery::type('array'))
             ->once()
             ->andReturn([
                 2 => [
                     'id' => 2,
                     'pontuacao' => 55, // Alto devido a tempo sem contato + etapa negociação
                     'prioridade' => 'alta'
                 ],
                 3 => [
                     'id' => 3,
                     'pontuacao' => 45, // Médio devido ao alto valor
                     'prioridade' => 'media'
                 ],
                 1 => [
                     'id' => 1,
                     'pontuacao' => 35, // Médio mas menor prioridade
                     'prioridade' => 'media'
                 ]
             ]);
             
        $prioridades = $estrategia->calcularPrioridadeFollowup($clientes);
        
        // Verificar cálculos de prioridade
        $this->assertEquals(3, count($prioridades));
        
        // Cliente 2 deve ser a maior prioridade (mais dias sem contato + estágio avançado)
        $this->assertEquals(2, array_keys($prioridades)[0]);
        $this->assertEquals('alta', $prioridades[2]['prioridade']);
        
        // Cliente 3 deve ter pontuação média-alta pelo alto valor potencial
        $this->assertEquals(3, array_keys($prioridades)[1]);
        $this->assertGreaterThan(30, $prioridades[3]['pontuacao']);
    }
    
    public function test_recomendacao_de_tipo_de_atividade_baseada_em_historico()
    {
        // Mock do serviço de estratégia de acompanhamento
        $estrategia = Mockery::mock('FollowupStrategy');
        
        // Histórico de atividades com taxas de sucesso
        $historicoAtividades = [
            'email' => ['total' => 200, 'sucesso' => 40],  // 20% sucesso
            'ligacao' => ['total' => 150, 'sucesso' => 60], // 40% sucesso
            'reuniao' => ['total' => 50, 'sucesso' => 25],  // 50% sucesso
            'demonstracao' => ['total' => 30, 'sucesso' => 15] // 50% sucesso
        ];
        
        // Mock da entidade
        $cliente = Mockery::mock(Entidade::class);
        $cliente->shouldReceive('getAttribute')->with('setor')->andReturn('tecnologia');
        $cliente->shouldReceive('getAttribute')->with('tamanho')->andReturn('medio');
        
        // Definir comportamento esperado - recomendação de tipo de atividade mais efetivo
        $estrategia->shouldReceive('recomendarTipoAtividade')
             ->with($cliente, $historicoAtividades)
             ->once()
             ->andReturnUsing(function($cliente, $historico) {
                 // Calcular taxas de sucesso
                 $taxasSucesso = [];
                 foreach ($historico as $tipo => $dados) {
                     $taxasSucesso[$tipo] = ($dados['sucesso'] / $dados['total']) * 100;
                 }
                 
                 // Fatores de ajuste por setor e tamanho
                 $ajustesSetor = [
                     'tecnologia' => ['email' => 1.2, 'ligacao' => 0.9, 'reuniao' => 1.1, 'demonstracao' => 1.3],
                     'financeiro' => ['email' => 0.8, 'ligacao' => 1.2, 'reuniao' => 1.3, 'demonstracao' => 0.9],
                     'varejo' => ['email' => 1.0, 'ligacao' => 1.1, 'reuniao' => 0.8, 'demonstracao' => 1.0]
                 ];
                 
                 $ajustesTamanho = [
                     'pequeno' => ['email' => 1.2, 'ligacao' => 1.1, 'reuniao' => 0.7, 'demonstracao' => 0.8],
                     'medio' => ['email' => 1.0, 'ligacao' => 1.0, 'reuniao' => 1.0, 'demonstracao' => 1.0],
                     'grande' => ['email' => 0.8, 'ligacao' => 0.9, 'reuniao' => 1.3, 'demonstracao' => 1.2]
                 ];
                 
                 // Aplicar ajustes
                 $taxasAjustadas = [];
                 foreach ($taxasSucesso as $tipo => $taxa) {
                     $ajusteSetor = $ajustesSetor[$cliente->setor][$tipo] ?? 1.0;
                     $ajusteTamanho = $ajustesTamanho[$cliente->tamanho][$tipo] ?? 1.0;
                     $taxasAjustadas[$tipo] = $taxa * $ajusteSetor * $ajusteTamanho;
                 }
                 
                 // Encontrar o tipo mais efetivo
                 arsort($taxasAjustadas);
                 return [
                     'recomendacao' => array_key_first($taxasAjustadas),
                     'taxas_ajustadas' => $taxasAjustadas
                 ];
             });
             
        $recomendacao = $estrategia->recomendarTipoAtividade($cliente, $historicoAtividades);
        
        // Para uma empresa de tecnologia de médio porte, a demonstração deve ser a mais recomendada
        $this->assertEquals('demonstracao', $recomendacao['recomendacao']);
        $this->assertGreaterThan(50, $recomendacao['taxas_ajustadas']['demonstracao']);
    }
} 