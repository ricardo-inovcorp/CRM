<?php

namespace Database\Seeders;

use App\Models\Funcao;
use Illuminate\Database\Seeder;

class FuncaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $funcoes = [
            // Cargos de direção
            'CEO',
            'Diretor Geral',
            'Diretor Executivo',
            'Diretor Financeiro',
            'Diretor de Marketing',
            'Diretor Comercial',
            'Diretor de Operações',
            'Diretor de Tecnologia',
            'Diretor de Recursos Humanos',
            
            // Cargos de gestão
            'Gerente',
            'Gerente de Projeto',
            'Gerente de Produto',
            'Gerente de Vendas',
            'Gerente de Marketing',
            'Gerente Administrativo',
            'Gerente Financeiro',
            'Gerente de RH',
            'Coordenador',
            'Supervisor',
            
            // Cargos técnicos
            'Analista',
            'Desenvolvedor',
            'Designer',
            'Técnico',
            'Engenheiro',
            'Consultor',
            'Especialista',
            
            // Cargos comerciais
            'Vendedor',
            'Representante Comercial',
            'Executivo de Contas',
            'Consultor de Vendas',
            'Assistente Comercial',
            
            // Cargos administrativos
            'Assistente Administrativo',
            'Secretário(a)',
            'Rececionista',
            'Auxiliar Administrativo',
            'Suporte Técnico',
            'Atendimento ao Cliente',
            'Outros'
        ];

        foreach ($funcoes as $funcao) {
            Funcao::firstOrCreate(['nome' => $funcao]);
        }
    }
} 