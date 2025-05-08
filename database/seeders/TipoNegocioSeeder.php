<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoNegocio;
class TipoNegocioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            'Venda de Produto',
            'Prestação de Serviço',
            'Consultoria',
            'Licenciamento',
            'Manutenção',
            'Projeto',
            'Renovação de Contrato',
            'Proposta Comercial',
            'Outros',
        ];
        foreach ($tipos as $tipo) {
            TipoNegocio::firstOrCreate(['nome' => $tipo]);
        }
    }
}
