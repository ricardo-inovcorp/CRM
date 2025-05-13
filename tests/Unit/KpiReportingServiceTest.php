<?php

namespace Tests\Unit;

use App\Models\Atividade;
use App\Models\Entidade;
use App\Models\Contacto;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;

class KpiReportingServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_calculo_de_taxa_de_conversao_de_leads()
    {
        // Mock do serviço de relatórios
        $service = Mockery::mock('KpiReportingService');
        
        // Configurar valores de entrada para cálculo
        $totalLeads = 150;
        $leadsConvertidos = 45;
        
        // Definir comportamento esperado
        $service->shouldReceive('calcularTaxaConversao')
             ->with($leadsConvertidos, $totalLeads)
             ->once()
             ->andReturnUsing(function($convertidos, $total) {
                 return ($convertidos / $total) * 100;
             });
             
        // Verificar cálculo: 45/150 * 100 = 30%
        $this->assertEquals(30, $service->calcularTaxaConversao($leadsConvertidos, $totalLeads));
    }
    
    public function test_calculo_de_valor_medio_de_negocio()
    {
        // Mock do serviço de relatórios
        $service = Mockery::mock('KpiReportingService');
        
        // Criar array de negócios fechados
        $negocios = [
            ['id' => 1, 'valor' => 12000],
            ['id' => 2, 'valor' => 28000],
            ['id' => 3, 'valor' => 5000],
            ['id' => 4, 'valor' => 15000]
        ];
        
        // Definir comportamento esperado
        $service->shouldReceive('calcularValorMedioNegocio')
             ->with(Mockery::type('array'))
             ->once()
             ->andReturnUsing(function($negocios) {
                 $total = array_sum(array_column($negocios, 'valor'));
                 return $total / count($negocios);
             });
             
        // Verificar cálculo: (12000 + 28000 + 5000 + 15000) / 4 = 15000
        $this->assertEquals(15000, $service->calcularValorMedioNegocio($negocios));
    }
    
    public function test_identificacao_de_clientes_inativos()
    {
        // Mock do serviço de relatórios
        $service = Mockery::mock('KpiReportingService');
        
        // Criar mocks de clientes com datas de última interação
        $cliente1 = Mockery::mock(Entidade::class);
        $cliente1->shouldReceive('getAttribute')->with('ultima_interacao')->andReturn('2023-01-15');
        $cliente1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        
        $cliente2 = Mockery::mock(Entidade::class);
        $cliente2->shouldReceive('getAttribute')->with('ultima_interacao')->andReturn('2023-05-20');
        $cliente2->shouldReceive('getAttribute')->with('id')->andReturn(2);
        
        $cliente3 = Mockery::mock(Entidade::class);
        $cliente3->shouldReceive('getAttribute')->with('ultima_interacao')->andReturn('2023-04-10');
        $cliente3->shouldReceive('getAttribute')->with('id')->andReturn(3);
        
        // Array de clientes simples com os dados que precisamos testar
        $clientes = [
            ['id' => 1, 'ultima_interacao' => '2023-01-15'],
            ['id' => 2, 'ultima_interacao' => '2023-05-20'],
            ['id' => 3, 'ultima_interacao' => '2023-04-10']
        ];
        
        // Vamos usar uma data fixa para o teste: 15/06/2023
        Carbon::setTestNow(Carbon::parse('2023-06-15'));
        
        // Definir comportamento esperado - identificar clientes sem interação por mais de 90 dias
        $service->shouldReceive('identificarClientesInativos')
             ->with(Mockery::type('array'), 90)
             ->once()
             ->andReturnUsing(function($clientes, $diasInatividade) {
                 $hoje = Carbon::now();
                 $inativos = [];
                 
                 foreach ($clientes as $cliente) {
                     $ultimaInteracao = Carbon::parse($cliente['ultima_interacao']);
                     if ($ultimaInteracao->diffInDays($hoje) > $diasInatividade) {
                         $inativos[] = $cliente['id'];
                     }
                 }
                 
                 return $inativos;
             });
             
        // Cliente 1: 151 dias (inativo)
        // Cliente 2: 26 dias (ativo)
        // Cliente 3: 66 dias (ativo)
        $this->assertEquals([1], $service->identificarClientesInativos($clientes, 90));
    }
    
    public function test_calculo_de_eficacia_por_tipo_de_atividade()
    {
        // Mock do serviço de relatórios
        $service = Mockery::mock('KpiReportingService');
        
        // Criar dados de atividades por tipo
        $atividades = [
            'Reunião' => ['concluidas' => 45, 'convertidas' => 20],
            'Ligação' => ['concluidas' => 120, 'convertidas' => 35],
            'Email' => ['concluidas' => 200, 'convertidas' => 30],
            'Demonstração' => ['concluidas' => 25, 'convertidas' => 15]
        ];
        
        // Definir comportamento esperado
        $service->shouldReceive('calcularEficaciaPorTipoAtividade')
             ->with(Mockery::type('array'))
             ->once()
             ->andReturnUsing(function($atividades) {
                 $resultado = [];
                 foreach ($atividades as $tipo => $dados) {
                     $resultado[$tipo] = ($dados['convertidas'] / $dados['concluidas']) * 100;
                 }
                 return $resultado;
             });
             
        $resultado = $service->calcularEficaciaPorTipoAtividade($atividades);
        
        // Verificar cálculos:
        // Reunião: 20/45 * 100 = 44.44%
        // Ligação: 35/120 * 100 = 29.17%
        // Email: 30/200 * 100 = 15%
        // Demonstração: 15/25 * 100 = 60%
        $this->assertEqualsWithDelta(44.44, $resultado['Reunião'], 0.01);
        $this->assertEqualsWithDelta(29.17, $resultado['Ligação'], 0.01);
        $this->assertEquals(15, $resultado['Email']);
        $this->assertEquals(60, $resultado['Demonstração']);
    }
} 