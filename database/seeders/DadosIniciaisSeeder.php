<?php

namespace Database\Seeders;

use App\Models\Funcao;
use App\Models\Pais;
use App\Models\TipoAtividade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Multitenancy\Models\Tenant;

class DadosIniciaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Este seeder deve ser executado depois que o tenant está no contexto
        
        // Criar países
        $paises = [
            ['nome' => 'Portugal', 'codigo' => 'PT'],
            ['nome' => 'Brasil', 'codigo' => 'BR'],
            ['nome' => 'Espanha', 'codigo' => 'ES'],
            ['nome' => 'França', 'codigo' => 'FR'],
            ['nome' => 'Alemanha', 'codigo' => 'DE'],
            ['nome' => 'Reino Unido', 'codigo' => 'GB'],
            ['nome' => 'Estados Unidos', 'codigo' => 'US'],
            ['nome' => 'Itália', 'codigo' => 'IT'],
        ];
        
        foreach ($paises as $pais) {
            Pais::create($pais);
        }
        
        // Criar funções para contactos
        $funcoes = [
            ['nome' => 'Diretor Geral'],
            ['nome' => 'Diretor Financeiro'],
            ['nome' => 'Diretor Comercial'],
            ['nome' => 'Diretor de Marketing'],
            ['nome' => 'Diretor de Recursos Humanos'],
            ['nome' => 'Gerente de Vendas'],
            ['nome' => 'Assistente Administrativo'],
            ['nome' => 'Técnico'],
            ['nome' => 'Outro'],
        ];
        
        foreach ($funcoes as $funcao) {
            Funcao::create($funcao);
        }
        
        // Criar tipos de atividade
        $tiposAtividade = [
            ['nome' => 'Reunião'],
            ['nome' => 'Chamada'],
            ['nome' => 'Email'],
            ['nome' => 'Tarefa'],
            ['nome' => 'Apresentação'],
            ['nome' => 'Almoço de Negócios'],
            ['nome' => 'Proposta Comercial'],
            ['nome' => 'Outro'],
        ];
        
        foreach ($tiposAtividade as $tipo) {
            TipoAtividade::create($tipo);
        }
    }
}
