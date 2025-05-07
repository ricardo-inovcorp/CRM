<?php

namespace Database\Seeders;

use App\Models\TipoAtividade;
use Illuminate\Database\Seeder;

class TipoAtividadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            // Reuniões
            'Reunião Presencial',
            'Videochamada',
            'Conferência Telefónica',
            
            // Comunicações
            'Chamada Telefónica',
            'Email',
            'Mensagem',
            
            // Vendas
            'Apresentação Comercial',
            'Demonstração de Produto',
            'Negociação',
            'Proposta Comercial',
            'Assinatura de Contrato',
            
            // Suporte
            'Suporte Técnico',
            'Resolução de Problemas',
            'Atendimento ao Cliente',
            
            // Follow-up
            'Follow-up de Venda',
            'Follow-up de Evento',
            'Follow-up Pós-Venda',
            
            // Eventos
            'Evento',
            'Webinar',
            'Workshop',
            'Formação',
            'Conferência',
            
            // Outros
            'Almoço de Negócios',
            'Networking',
            'Outro'
        ];

        foreach ($tipos as $tipo) {
            TipoAtividade::firstOrCreate(['nome' => $tipo]);
        }
    }
} 