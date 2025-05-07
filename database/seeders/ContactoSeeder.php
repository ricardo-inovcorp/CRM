<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Funcao;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter uma função (exemplo: Diretor)
        $funcao = Funcao::firstOrCreate(['nome' => 'Diretor']);
        
        // Pegar todos os tenants
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->command->info('Nenhum tenant encontrado. Seeders devem ser executados na ordem correta.');
            return;
        }
        
        // Limpar contactos existentes
        DB::table('contactos')->delete();
        
        // Para cada tenant, criar contatos para suas entidades
        foreach ($tenants as $tenant) {
            $this->command->info("Criando contactos para o tenant: {$tenant->name}");
            
            // Obter entidades deste tenant
            $entidades = Entidade::where('tenant_id', $tenant->id)->get();
            
            if ($entidades->isEmpty()) {
                $this->command->info("Nenhuma entidade encontrada para o tenant {$tenant->name}");
                continue;
            }
            
            foreach ($entidades as $entidade) {
                // Criar vários contatos para cada entidade
                for ($i = 1; $i <= 3; $i++) {
                    Contacto::create([
                        'nome' => "Contato {$i}",
                        'apelido' => "Sobrenome {$i}",
                        'entidade_id' => $entidade->id,
                        'funcao_id' => $funcao->id,
                        'telefone' => "21{$i}000000",
                        'telemovel' => "91{$i}000000",
                        'email' => "contato{$i}@" . strtolower(str_replace(' ', '', $entidade->nome)) . ".pt",
                        'observacoes' => "Contato de teste {$i} para {$entidade->nome}",
                        'estado' => 'Ativo',
                        'tenant_id' => $tenant->id, // Garantir que o tenant_id é definido
                    ]);
                }
            }
            
            $this->command->info("Contactos criados para o tenant {$tenant->name}");
        }
        
        $this->command->info('Todos os contactos foram criados com sucesso!');
    }
}
