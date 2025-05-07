<?php

namespace Database\Seeders;

use App\Models\Atividade;
use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Tenant;
use App\Models\TipoAtividade;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AtividadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tipos de atividade
        $tipoReuniao = TipoAtividade::firstOrCreate(['nome' => 'Reunião']);
        $tipoAlmoco = TipoAtividade::firstOrCreate(['nome' => 'Almoço de Negócios']);
        $tipoEmail = TipoAtividade::firstOrCreate(['nome' => 'Email']);
        $tipoChamada = TipoAtividade::firstOrCreate(['nome' => 'Chamada']);
        $tipoTarefa = TipoAtividade::firstOrCreate(['nome' => 'Tarefa']);
        
        // Obter tenants
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->command->info('Nenhum tenant encontrado. Execute primeiro o TenantSeeder.');
            return;
        }
        
        // Limpar atividades existentes
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('atividade_participante')->delete();
        DB::table('atividade_conhecimento')->delete();
        DB::table('atividades')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Para cada tenant, criar suas próprias atividades
        foreach ($tenants as $tenant) {
            $this->command->info("Criando atividades para o tenant: {$tenant->name}");
            
            // Obter entidades, contatos e usuários deste tenant
            $entidades = Entidade::where('tenant_id', $tenant->id)->get();
            $contactos = Contacto::where('tenant_id', $tenant->id)->get();
            $users = User::where('tenant_id', $tenant->id)->get();
            
            if ($entidades->isEmpty() || $contactos->isEmpty() || $users->isEmpty()) {
                $this->command->info("Dados insuficientes para o tenant {$tenant->name}");
                continue;
            }
            
            // Tipos de atividade para variar os exemplos
            $tipos = [$tipoReuniao, $tipoAlmoco, $tipoEmail, $tipoChamada, $tipoTarefa];
            
            // Para cada entidade, criar algumas atividades
            foreach ($entidades as $index => $entidade) {
                // Obter contatos desta entidade
                $contatosEntidade = $contactos->where('entidade_id', $entidade->id);
                
                if ($contatosEntidade->isEmpty()) {
                    continue;
                }
                
                // Criar atividades de diferentes tipos
                for ($i = 0; $i < 3; $i++) {
                    $tipo = $tipos[array_rand($tipos)];
                    $contacto = $contatosEntidade->random();
                    $user = $users->random();
                    
                    // Criar a atividade
                    $atividade = Atividade::create([
                        'data' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                        'hora' => sprintf('%02d:%02d', rand(8, 18), rand(0, 59)),
                        'duracao' => rand(15, 120),
                        'entidade_id' => $entidade->id,
                        'contacto_id' => $contacto->id,
                        'tipo_id' => $tipo->id,
                        'descricao' => "Atividade de {$tipo->nome} com {$contacto->nome} da {$entidade->nome}",
                        'tenant_id' => $tenant->id,
                    ]);
                    
                    // Adicionar participantes e conhecimento
                    $atividade->participantes()->attach($user);
                    
                    // Adicionar outro usuário ao conhecimento (se houver mais de um)
                    if ($users->count() > 1) {
                        $outroUser = $users->where('id', '!=', $user->id)->first();
                        $atividade->conhecimento()->attach($outroUser);
                    }
                }
            }
            
            $this->command->info("Atividades criadas para o tenant {$tenant->name}");
        }
        
        $this->command->info('Todas as atividades foram criadas com sucesso!');
    }
}
