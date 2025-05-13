<?php

namespace Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DateTimeUtilsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Fixar a data e hora atual para os testes
        Carbon::setTestNow(Carbon::parse('2023-07-15 14:30:00'));
    }

    protected function tearDown(): void
    {
        // Limpar a data e hora fixada
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_carbon_parse_cria_instancia_de_data()
    {
        $data = Carbon::parse('2023-05-10');
        
        $this->assertInstanceOf(Carbon::class, $data);
        $this->assertEquals(2023, $data->year);
        $this->assertEquals(5, $data->month);
        $this->assertEquals(10, $data->day);
    }
    
    public function test_carbon_now_retorna_data_atual()
    {
        $agora = Carbon::now();
        
        $this->assertEquals('2023-07-15', $agora->toDateString());
        $this->assertEquals('14:30:00', $agora->toTimeString());
    }
    
    public function test_formatar_data_em_diferentes_formatos()
    {
        $data = Carbon::parse('2023-05-10 15:30:45');
        
        $this->assertEquals('2023-05-10', $data->toDateString());
        $this->assertEquals('10/05/2023', $data->format('d/m/Y'));
        $this->assertEquals('May 10, 2023', $data->format('F j, Y'));
        $this->assertEquals('15:30:45', $data->toTimeString());
    }
    
    public function test_adicionar_e_subtrair_intervalos_de_tempo()
    {
        $data = Carbon::parse('2023-05-10');
        
        $novaData = $data->addDays(5);
        $this->assertEquals('2023-05-15', $novaData->toDateString());
        
        $novaData = $data->subMonths(2);
        $this->assertEquals('2023-03-15', $novaData->toDateString());
        
        $novaData = $data->addYear();
        $this->assertEquals('2024-03-15', $novaData->toDateString());
    }
    
    public function test_comparacao_entre_datas()
    {
        $data1 = Carbon::parse('2023-05-10');
        $data2 = Carbon::parse('2023-06-15');
        
        $this->assertTrue($data1->lessThan($data2));
        $this->assertTrue($data2->greaterThan($data1));
        
        // Verificar a diferença em dias
        $diff = $data1->diffInDays($data2);
        $this->assertEquals(36, $diff);
    }
} 