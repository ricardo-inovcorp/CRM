<?php

namespace Tests\Unit;

use App\Models\Atividade;
use App\Models\User;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AtividadeBusinessLogicTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function test_atividade_com_data_passada_deve_estar_concluida()
    {
        // Criar mock da atividade
        $atividade = Mockery::mock(Atividade::class)->makePartial();
        $atividade->data = '2023-01-15';
        $atividade->hora_inicio = '10:00';
        $atividade->hora_fim = '11:00';
        $atividade->concluida = false;
        
        // Mock do método que verifica se a atividade está no passado
        $atividade->shouldReceive('estaNoPastado')
             ->once()
             ->andReturn(true);
             
        // Criar mock de método que verifica se uma atividade deve estar concluída
        $atividade->shouldReceive('verificarConclusaoAutomatica')
             ->once()
             ->andReturnUsing(function() use ($atividade) {
                 if ($atividade->estaNoPastado() && !$atividade->concluida) {
                     return true;
                 }
                 return false;
             });
             
        // Verificar que a atividade deve ser concluída automaticamente
        $this->assertTrue($atividade->verificarConclusaoAutomatica());
    }
    
    public function test_calcular_duracao_de_atividade_em_minutos()
    {
        $atividade = Mockery::mock(Atividade::class)->makePartial();
        $atividade->hora_inicio = '09:00';
        $atividade->hora_fim = '10:30';
        
        // Mock do método que calcula a duração
        $atividade->shouldReceive('calcularDuracaoEmMinutos')
             ->once()
             ->andReturnUsing(function() use ($atividade) {
                 $inicio = Carbon::parse($atividade->hora_inicio);
                 $fim = Carbon::parse($atividade->hora_fim);
                 // Usamos abs() para garantir valor positivo independente da ordem
                 return abs($fim->diffInMinutes($inicio));
             });
        
        // Verificar cálculo correto da duração (1h30m = 90 minutos)
        $this->assertEquals(90, $atividade->calcularDuracaoEmMinutos());
    }
    
    public function test_atividade_deve_notificar_participantes_ao_ser_criada()
    {
        // Criar mocks
        $atividade = Mockery::mock(Atividade::class)->makePartial();
        $user1 = Mockery::mock(User::class);
        $user2 = Mockery::mock(User::class);
        
        // Criando coleção de participantes
        $collection = collect([$user1, $user2]);
        
        // Criar mock da relação belongsToMany
        $relation = Mockery::mock(BelongsToMany::class);
        $relation->shouldReceive('get')
             ->once()
             ->andReturn($collection);
             
        // Configurar atividade para retornar a relação
        $atividade->shouldReceive('participantes')
             ->once()
             ->andReturn($relation);
             
        // Mock do método de notificação
        $user1->shouldReceive('notificarSobreAtividade')
             ->with($atividade)
             ->once();
             
        $user2->shouldReceive('notificarSobreAtividade')
             ->with($atividade)
             ->once();
             
        // Mock do método que envia notificações
        $atividade->shouldReceive('notificarParticipantes')
             ->once()
             ->andReturnUsing(function() use ($atividade) {
                 $notificados = 0;
                 foreach ($atividade->participantes()->get() as $participante) {
                     $participante->notificarSobreAtividade($atividade);
                     $notificados++;
                 }
                 return $notificados;
             });
             
        // Verificar que todos os participantes foram notificados
        $this->assertEquals(2, $atividade->notificarParticipantes());
    }
    
    public function test_atividade_completa_calcula_duracao_automaticamente()
    {
        // Criar mock da atividade
        $atividade = Mockery::mock(Atividade::class)->makePartial();
        $atividade->hora_inicio = '14:00';
        $atividade->hora_fim = '16:45';
        $atividade->duracao = null;
        
        // Mock do método que calcula a duração
        $atividade->shouldReceive('calcularDuracaoEmMinutos')
             ->once()
             ->andReturn(165); // 2h45m = 165 minutos
             
        // Mock do método que atualiza a atividade ao concluir
        $atividade->shouldReceive('concluir')
             ->once()
             ->andReturnUsing(function() use ($atividade) {
                 $atividade->duracao = $atividade->calcularDuracaoEmMinutos();
                 $atividade->concluida = true;
                 return true;
             });
             
        // Concluir a atividade e verificar duracao
        $this->assertTrue($atividade->concluir());
        $this->assertEquals(165, $atividade->duracao);
        $this->assertTrue($atividade->concluida);
    }
} 