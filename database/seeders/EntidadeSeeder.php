<?php

namespace Database\Seeders;

use App\Models\Entidade;
use App\Models\Pais;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter um país (Portugal, por exemplo)
        $pais = Pais::firstOrCreate(['nome' => 'Portugal']);

        // Criar entidades de exemplo
        $entidades = [
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

        foreach ($entidades as $entidade) {
            Entidade::firstOrCreate(['nome' => $entidade['nome']], $entidade);
        }
    }
}
