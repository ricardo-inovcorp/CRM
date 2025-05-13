<?php

namespace Tests\Unit;

use App\Models\Atividade;
use App\Models\Entidade;
use App\Models\Contacto;
use App\Models\TipoAtividade;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtividadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_atividade_belongs_to_entidade()
    {
        $entidade = Entidade::factory()->create();
        $atividade = Atividade::factory()->create([
            'entidade_id' => $entidade->id
        ]);
        
        $this->assertEquals($entidade->id, $atividade->entidade->id);
    }
    
    public function test_atividade_belongs_to_contacto()
    {
        $contacto = Contacto::factory()->create();
        $atividade = Atividade::factory()->create([
            'contacto_id' => $contacto->id
        ]);
        
        $this->assertEquals($contacto->id, $atividade->contacto->id);
    }
    
    public function test_atividade_belongs_to_tipo()
    {
        $tipo = TipoAtividade::factory()->create();
        $atividade = Atividade::factory()->create([
            'tipo_id' => $tipo->id
        ]);
        
        $this->assertEquals($tipo->id, $atividade->tipo->id);
    }
    
    public function test_atividade_has_participantes()
    {
        $atividade = Atividade::factory()->create();
        $user = User::factory()->create();
        
        $atividade->participantes()->attach($user->id);
        
        $this->assertEquals(1, $atividade->participantes->count());
        $this->assertEquals($user->id, $atividade->participantes->first()->id);
    }
    
    public function test_atividade_has_conhecimento()
    {
        $atividade = Atividade::factory()->create();
        $user = User::factory()->create();
        
        $atividade->conhecimento()->attach($user->id);
        
        $this->assertEquals(1, $atividade->conhecimento->count());
        $this->assertEquals($user->id, $atividade->conhecimento->first()->id);
    }
    
    public function test_formato_de_duracao_retorna_0h_quando_nao_ha_duracao()
    {
        $atividade = Atividade::factory()->create([
            'duracao' => null
        ]);
        
        $this->assertEquals('0h', $atividade->duracaoFormatada());
    }
    
    public function test_formato_de_duracao_retorna_apenas_horas_quando_minutos_sao_zero()
    {
        $atividade = Atividade::factory()->create([
            'duracao' => 120 // 2 horas
        ]);
        
        $this->assertEquals('2h', $atividade->duracaoFormatada());
    }
    
    public function test_formato_de_duracao_retorna_apenas_minutos_quando_horas_sao_zero()
    {
        $atividade = Atividade::factory()->create([
            'duracao' => 30 // 30 minutos
        ]);
        
        $this->assertEquals('30m', $atividade->duracaoFormatada());
    }
    
    public function test_formato_de_duracao_retorna_horas_e_minutos_quando_ambos_estao_presentes()
    {
        $atividade = Atividade::factory()->create([
            'duracao' => 150 // 2 horas e 30 minutos
        ]);
        
        $this->assertEquals('2h 30m', $atividade->duracaoFormatada());
    }
    
    public function test_atividade_pertence_a_um_tenant()
    {
        $tenant = Tenant::factory()->create();
        $atividade = Atividade::factory()->create([
            'tenant_id' => $tenant->id
        ]);
        
        $this->assertEquals($tenant->id, $atividade->tenant->id);
    }
} 