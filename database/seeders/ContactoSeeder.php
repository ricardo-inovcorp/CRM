<?php

namespace Database\Seeders;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Funcao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criando funções de exemplo
        $funcoes = [
            ['nome' => 'Diretor Geral'],
            ['nome' => 'Gerente'],
            ['nome' => 'Comercial'],
            ['nome' => 'Técnico'],
            ['nome' => 'Administrativo'],
        ];

        foreach ($funcoes as $funcao) {
            Funcao::firstOrCreate($funcao);
        }

        // Obter funções e entidades existentes
        $diretorGeral = Funcao::where('nome', 'Diretor Geral')->first();
        $gerente = Funcao::where('nome', 'Gerente')->first();
        $comercial = Funcao::where('nome', 'Comercial')->first();

        $empresaABC = Entidade::where('nome', 'Empresa ABC')->first();
        $consultoriaXYZ = Entidade::where('nome', 'Consultoria XYZ')->first();
        $industriasLMN = Entidade::where('nome', 'Indústrias LMN')->first();

        // Garantir que as entidades existem
        if (!$empresaABC || !$consultoriaXYZ || !$industriasLMN) {
            $this->command->info('Por favor, execute primeiro o EntidadeSeeder');
            return;
        }

        // Criar contactos de exemplo
        $contactos = [
            [
                'nome' => 'João',
                'apelido' => 'Silva',
                'entidade_id' => $empresaABC->id,
                'funcao_id' => $diretorGeral->id,
                'telefone' => '212345678',
                'telemovel' => '912345678',
                'email' => 'joao.silva@empresaabc.pt',
                'observacoes' => 'Contacto principal',
                'estado' => 'Ativo',
            ],
            [
                'nome' => 'Maria',
                'apelido' => 'Santos',
                'entidade_id' => $empresaABC->id,
                'funcao_id' => $comercial->id,
                'telefone' => '212345679',
                'telemovel' => '923456789',
                'email' => 'maria.santos@empresaabc.pt',
                'observacoes' => 'Departamento comercial',
                'estado' => 'Ativo',
            ],
            [
                'nome' => 'António',
                'apelido' => 'Costa',
                'entidade_id' => $consultoriaXYZ->id,
                'funcao_id' => $gerente->id,
                'telefone' => '213456780',
                'telemovel' => '934567890',
                'email' => 'antonio.costa@xyz.pt',
                'observacoes' => 'Contacto preferencial',
                'estado' => 'Ativo',
            ],
            [
                'nome' => 'Paulo',
                'apelido' => 'Ferreira',
                'entidade_id' => $industriasLMN->id,
                'funcao_id' => $diretorGeral->id,
                'telefone' => '253123457',
                'telemovel' => '961234567',
                'email' => 'paulo.ferreira@lmn.pt',
                'observacoes' => 'Decisor principal',
                'estado' => 'Inativo',
            ],
        ];

        foreach ($contactos as $contacto) {
            Contacto::firstOrCreate(
                ['email' => $contacto['email']],
                $contacto
            );
        }
    }
}
