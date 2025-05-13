<?php

namespace Tests\Unit;

use App\Models\Atividade;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    /**
     * Teste para verificar o formato de data em português
     */
    public function test_formatacao_data_em_portugues()
    {
        // Testar com data específica
        $data = Carbon::parse('2023-05-15');
        
        // Usar a forma direta do Carbon para formatar a data
        $formatoBrasil = $data->format('d/m/Y');
        
        // Validar o formato esperado
        $this->assertEquals('15/05/2023', $formatoBrasil);
    }
    
    /**
     * Teste para verificar a tradução de dia da semana para português usando Carbon
     */
    public function test_nome_dia_semana_com_carbon()
    {
        Carbon::setLocale('pt_BR');
        
        // Testar com data específica (uma segunda-feira)
        $data = Carbon::parse('2023-05-15'); // Segunda-feira
        
        // Verificar se contém alguma forma de "segunda"
        $diaFormatado = $data->translatedFormat('l');
        $this->assertStringContainsStringIgnoringCase('seg', $diaFormatado);
    }
    
    /**
     * Teste para validar cálculo de duração
     */
    public function test_calculo_duracao_entre_horas()
    {
        // Definir horários início e fim
        $inicio = Carbon::parse('10:00');
        $fim = Carbon::parse('11:30');
        
        // Calcular diferença em minutos usando a forma correta (sempre positivo)
        $duracaoMinutos = abs($inicio->diffInMinutes($fim));
        
        // Verificar resultado
        $this->assertEquals(90, $duracaoMinutos);
        
        // Converter para horas e minutos formatados
        $horas = floor($duracaoMinutos / 60);
        $minutos = $duracaoMinutos % 60;
        
        $formatado = $horas . 'h';
        if ($minutos > 0) {
            $formatado .= ' ' . $minutos . 'm';
        }
        
        // Verificar formato
        $this->assertEquals('1h 30m', $formatado);
    }
} 