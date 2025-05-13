<?php

namespace Tests\Feature;

use App\Models\Atividade;
use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\Entidade;
use App\Models\Contacto;
use App\Models\TipoAtividade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtividadeControllerTest extends TestCase
{
    use RefreshDatabase;
    
    protected User $user;
    protected Tenant $tenant;
    
    public function setUp(): void
    {
        parent::setUp();
        
        // Configurar tenant e usuário
        $this->tenant = Tenant::factory()->create();
        $role = Role::factory()->create(['slug' => 'admin']);
        
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_admin' => true
        ]);
        
        $this->user->roles()->attach($role->id);
    }
    
    public function test_index_shows_atividades_list()
    {
        // Criar algumas atividades
        $entidade = Entidade::factory()->create(['tenant_id' => $this->tenant->id]);
        $tipo = TipoAtividade::factory()->create();
        
        $atividade = Atividade::factory()->create([
            'entidade_id' => $entidade->id,
            'tipo_id' => $tipo->id,
            'tenant_id' => $this->tenant->id,
            'descricao' => 'Reunião de teste'
        ]);
        
        // Acessar a página de listagem
        $response = $this->actingAs($this->user)
                        ->get(route('atividades.index'));
        
        $response->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->component('atividades/Index')
                    ->has('atividades.data', 1)
                    ->where('atividades.data.0.id', $atividade->id)
                );
    }
    
    public function test_store_creates_new_atividade()
    {
        // Criar dependências
        $entidade = Entidade::factory()->create(['tenant_id' => $this->tenant->id]);
        $contacto = Contacto::factory()->create(['entidade_id' => $entidade->id]);
        $tipo = TipoAtividade::factory()->create();
        
        // Dados para criar atividade
        $atividadeData = [
            'data' => '2023-05-15',
            'hora' => '10:00',
            'duracao' => 60,
            'entidade_id' => $entidade->id,
            'contacto_id' => $contacto->id,
            'tipo_id' => $tipo->id,
            'descricao' => 'Nova atividade de teste',
            'participantes' => [$this->user->id]
        ];
        
        // Enviar requisição para criar
        $response = $this->actingAs($this->user)
                        ->post(route('atividades.store'), $atividadeData);
        
        // Verificar redirecionamento
        $response->assertRedirect(route('calendario.index'))
                ->assertSessionHas('success');
        
        // Verificar se a atividade foi criada
        $this->assertDatabaseHas('atividades', [
            'descricao' => 'Nova atividade de teste',
            'tenant_id' => $this->tenant->id
        ]);
        
        // Verificar se os participantes foram vinculados
        $atividade = Atividade::where('descricao', 'Nova atividade de teste')->first();
        $this->assertTrue($atividade->participantes->contains($this->user->id));
    }
    
    public function test_show_displays_atividade_details()
    {
        // Criar atividade
        $entidade = Entidade::factory()->create(['tenant_id' => $this->tenant->id]);
        $tipo = TipoAtividade::factory()->create();
        
        $atividade = Atividade::factory()->create([
            'entidade_id' => $entidade->id,
            'tipo_id' => $tipo->id,
            'tenant_id' => $this->tenant->id,
            'descricao' => 'Atividade para visualizar'
        ]);
        
        // Acessar detalhes
        $response = $this->actingAs($this->user)
                        ->get(route('atividades.show', $atividade));
        
        $response->assertStatus(200)
                ->assertInertia(fn ($page) => $page
                    ->component('atividades/Show')
                    ->where('atividade.id', $atividade->id)
                );
    }
    
    public function test_update_modifies_atividade()
    {
        // Criar atividade
        $entidade = Entidade::factory()->create(['tenant_id' => $this->tenant->id]);
        $tipo = TipoAtividade::factory()->create();
        $novoTipo = TipoAtividade::factory()->create();
        
        $atividade = Atividade::factory()->create([
            'entidade_id' => $entidade->id,
            'tipo_id' => $tipo->id,
            'tenant_id' => $this->tenant->id,
            'descricao' => 'Atividade original',
            'duracao' => 30
        ]);
        
        // Dados para atualizar
        $updateData = [
            'data' => $atividade->data->format('Y-m-d'),
            'hora' => $atividade->hora->format('H:i'),
            'duracao' => 60, // Alterado
            'entidade_id' => $entidade->id,
            'tipo_id' => $novoTipo->id, // Alterado
            'descricao' => 'Atividade atualizada', // Alterado
            'participantes' => [$this->user->id]
        ];
        
        // Enviar requisição para atualizar
        $response = $this->actingAs($this->user)
                        ->put(route('atividades.update', $atividade), $updateData);
        
        // Verificar redirecionamento
        $response->assertRedirect(route('atividades.index'))
                ->assertSessionHas('success');
        
        // Verificar se a atividade foi atualizada
        $this->assertDatabaseHas('atividades', [
            'id' => $atividade->id,
            'descricao' => 'Atividade atualizada',
            'duracao' => 60,
            'tipo_id' => $novoTipo->id
        ]);
    }
    
    public function test_destroy_deletes_atividade()
    {
        // Criar atividade
        $entidade = Entidade::factory()->create(['tenant_id' => $this->tenant->id]);
        $tipo = TipoAtividade::factory()->create();
        
        $atividade = Atividade::factory()->create([
            'entidade_id' => $entidade->id,
            'tipo_id' => $tipo->id,
            'tenant_id' => $this->tenant->id
        ]);
        
        // Enviar requisição para excluir
        $response = $this->actingAs($this->user)
                        ->delete(route('atividades.destroy', $atividade));
        
        // Verificar redirecionamento
        $response->assertRedirect(route('atividades.index'))
                ->assertSessionHas('success');
        
        // Verificar se a atividade foi removida
        $this->assertDatabaseMissing('atividades', [
            'id' => $atividade->id
        ]);
    }
} 