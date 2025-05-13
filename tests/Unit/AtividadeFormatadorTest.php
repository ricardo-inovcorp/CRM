<?php

namespace Tests\Unit;

use App\Models\Atividade;
use PHPUnit\Framework\TestCase;

class AtividadeFormatadorTest extends TestCase
{
    public function test_duracao_formatada_retorna_0h_quando_duracao_nula()
    {
        $atividade = new Atividade();
        $atividade->duracao = null;
        
        $this->assertEquals('0h', $atividade->duracaoFormatada());
    }
    
    public function test_duracao_formatada_retorna_apenas_horas_quando_sem_minutos()
    {
        $atividade = new Atividade();
        $atividade->duracao = 120; // 2 horas
        
        $this->assertEquals('2h', $atividade->duracaoFormatada());
    }
    
    public function test_duracao_formatada_retorna_apenas_minutos_quando_sem_horas()
    {
        $atividade = new Atividade();
        $atividade->duracao = 30; // 30 minutos
        
        $this->assertEquals('30m', $atividade->duracaoFormatada());
    }
    
    public function test_duracao_formatada_retorna_horas_e_minutos_quando_ambos_presentes()
    {
        $atividade = new Atividade();
        $atividade->duracao = 150; // 2 horas e 30 minutos
        
        $this->assertEquals('2h 30m', $atividade->duracaoFormatada());
    }
} 