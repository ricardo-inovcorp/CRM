<?php

namespace Database\Seeders;

use App\Models\Atividade;
use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\TipoAtividade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtividadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar tipos de atividade
        $tiposAtividade = [
            ['nome' => 'Reunião'],
            ['nome' => 'Chamada'],
            ['nome' => 'Email'],
            ['nome' => 'Apresentação'],
            ['nome' => 'Proposta'],
        ];

        foreach ($tiposAtividade as $tipo) {
            TipoAtividade::firstOrCreate($tipo);
        }

        // Obter tipos de atividade criados
        $reuniao = TipoAtividade::where('nome', 'Reunião')->first();
        $chamada = TipoAtividade::where('nome', 'Chamada')->first();
        $email = TipoAtividade::where('nome', 'Email')->first();
        $proposta = TipoAtividade::where('nome', 'Proposta')->first();

        // Obter entidades e contactos
        $empresaABC = Entidade::where('nome', 'Empresa ABC')->first();
        $consultoriaXYZ = Entidade::where('nome', 'Consultoria XYZ')->first();
        $industriasLMN = Entidade::where('nome', 'Indústrias LMN')->first();

        $joao = Contacto::where('email', 'joao.silva@empresaabc.pt')->first();
        $antonio = Contacto::where('email', 'antonio.costa@xyz.pt')->first();
        $paulo = Contacto::where('email', 'paulo.ferreira@lmn.pt')->first();

        // Verificar se existem entidades e contactos necessários
        if (!$empresaABC || !$consultoriaXYZ || !$industriasLMN || !$joao || !$antonio || !$paulo) {
            $this->command->info('Por favor, execute primeiro o EntidadeSeeder e o ContactoSeeder');
            return;
        }

        // Criar atividades de exemplo
        $atividades = [
            [
                'data' => now()->subDays(15)->format('Y-m-d'),
                'hora' => '09:00',
                'duracao' => 90, // 1h30min em minutos
                'entidade_id' => $empresaABC->id,
                'contacto_id' => $joao->id,
                'tipo_id' => $reuniao->id,
                'descricao' => 'Reunião inicial - Apresentação de serviços e identificação de necessidades',
            ],
            [
                'data' => now()->subDays(10)->format('Y-m-d'),
                'hora' => '14:00',
                'duracao' => 60, // 1h em minutos
                'entidade_id' => $empresaABC->id,
                'contacto_id' => $joao->id,
                'tipo_id' => $proposta->id,
                'descricao' => 'Envio de proposta - Proposta comercial baseada na reunião inicial',
            ],
            [
                'data' => now()->subDays(5)->format('Y-m-d'),
                'hora' => '11:00',
                'duracao' => 30, // 30min em minutos
                'entidade_id' => $empresaABC->id,
                'contacto_id' => $joao->id,
                'tipo_id' => $chamada->id,
                'descricao' => 'Chamada de follow-up - Verificar se receberam a proposta e esclarecer dúvidas',
            ],
            [
                'data' => now()->addDays(3)->format('Y-m-d'),
                'hora' => '10:00',
                'duracao' => 90, // 1h30min em minutos
                'entidade_id' => $consultoriaXYZ->id,
                'contacto_id' => $antonio->id,
                'tipo_id' => $reuniao->id,
                'descricao' => 'Reunião com consultoria - Explorar possíveis parcerias',
            ],
            [
                'data' => now()->subDays(2)->format('Y-m-d'),
                'hora' => '15:00',
                'duracao' => 30, // 30min em minutos
                'entidade_id' => $industriasLMN->id,
                'contacto_id' => $paulo->id,
                'tipo_id' => $email->id,
                'descricao' => 'Email de apresentação - Envio de informações sobre produtos e serviços',
            ],
        ];

        foreach ($atividades as $atividade) {
            Atividade::firstOrCreate(
                [
                    'data' => $atividade['data'],
                    'entidade_id' => $atividade['entidade_id'],
                    'hora' => $atividade['hora'],
                ],
                $atividade
            );
        }
    }
}
