<?php

namespace Database\Seeders;

use App\Models\Entidade;
use App\Models\Pais;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter um país (Portugal, por exemplo)
        $pais = Pais::firstOrCreate(['nome' => 'Portugal']);
        
        // Pegar todos os tenants para criar entidades para cada um
        $tenants = Tenant::all();
        
        // Se não houver tenants, não há como prosseguir
        if ($tenants->isEmpty()) {
            $this->command->info('Nenhum tenant encontrado. Execute TenantSeeder primeiro.');
            return;
        }
        
        // Limpar entidades existentes
        DB::table('entidades')->delete();
        
        // Dados base das entidades
        $entidadesBase = [
            [
                'nome' => 'Empresa ABC',
                'morada' => 'Rua Principal, 123',
                'codigo_postal' => '1000-001',
                'localidade' => 'Lisboa',
                'pais_id' => $pais->id,
                'telefone' => '212345678',
                'email' => 'info@empresaabc.pt',
                'website' => 'https://www.empresaabc.pt',
                'observacoes' => 'Cliente desde 2020',
                'estado' => 'Ativo',
            ],
            [
                'nome' => 'Consultoria XYZ',
                'morada' => 'Avenida da Liberdade, 456',
                'codigo_postal' => '1250-147',
                'localidade' => 'Lisboa',
                'pais_id' => $pais->id,
                'telefone' => '213456789',
                'email' => 'contact@xyz.pt',
                'website' => 'https://www.xyz.pt',
                'observacoes' => 'Parceiro estratégico',
                'estado' => 'Ativo',
            ],
            [
                'nome' => 'Indústrias LMN',
                'morada' => 'Zona Industrial, Lote 78',
                'codigo_postal' => '4700-087',
                'localidade' => 'Braga',
                'pais_id' => $pais->id,
                'telefone' => '253123456',
                'email' => 'geral@lmn.pt',
                'website' => 'https://www.lmn.pt',
                'observacoes' => 'Cliente potencial',
                'estado' => 'Inativo',
            ],
        ];

        // Para cada tenant, criar suas próprias entidades
        foreach ($tenants as $tenant) {
            $this->command->info("Criando entidades para o tenant: {$tenant->name}");
            
            foreach ($entidadesBase as $index => $entidade) {
                // Adicionar o tenant_id e um sufixo para diferenciar
                $entidade['tenant_id'] = $tenant->id;
                $entidade['nome'] = $entidade['nome'] . ' - ' . $tenant->name;
                $entidade['email'] = str_replace('@', '-' . $tenant->id . '@', $entidade['email']);
                
                Entidade::create($entidade);
            }
        }
        
        $this->command->info('Entidades criadas com sucesso para todos os tenants!');
    }
}
