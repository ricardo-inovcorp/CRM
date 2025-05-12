<?php

namespace Database\Seeders;

use App\Models\Negocio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RemoveRedundantEstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Iniciando seeder para corrigir estados de negócios');

        // 1. Listar todos os negócios para diagnóstico
        $todosNegocios = Negocio::select('id', 'nome', 'estado')->get();
        Log::info('Todos os negócios antes da correção:', $todosNegocios->toArray());

        // 2. Verificar negócios com estado "novo"
        $negociosNovo = Negocio::where('estado', 'novo')->get();
        Log::info('Negócios com estado "novo":', $negociosNovo->toArray());

        // 3. Remover estado "novo" se for um estado fantasma/duplicado
        if ($negociosNovo->count() > 0) {
            // Confirmar que estes negócios existem e não são duplicados
            foreach ($negociosNovo as $negocio) {
                Log::info("Verificando negócio id={$negocio->id}, estado={$negocio->estado}");
                
                // Verificar se este negócio já tem outro estado no sistema
                $duplicados = Negocio::where('id', '!=', $negocio->id)
                    ->where(function($query) use ($negocio) {
                        $query->where('nome', $negocio->nome)
                              ->where('entidade_id', $negocio->entidade_id)
                              ->where('valor', $negocio->valor);
                    })
                    ->get();
                
                if ($duplicados->count() > 0) {
                    Log::warning("Encontrado possível duplicado para o negócio id={$negocio->id}:", 
                        $duplicados->toArray());
                    
                    // Remover o negócio com estado "novo" se for duplicado
                    $negocio->delete();
                    Log::info("Removido negócio duplicado id={$negocio->id}");
                }
            }
        }

        // 4. Verificar a consistência dos totais
        $totalNegocios = Negocio::count();
        $negociosPorEstado = Negocio::select('estado')
            ->where('estado', '!=', '')
            ->whereNotNull('estado')
            ->get()
            ->groupBy('estado');
            
        $estadosNegocios = [];
        foreach ($negociosPorEstado as $estado => $negocios) {
            $estadosNegocios[] = [
                'estado' => $estado,
                'count' => $negocios->count()
            ];
        }
        
        Log::info("Verificação final:");
        Log::info("Total de negócios: $totalNegocios");
        Log::info("Estados de negócios:", $estadosNegocios);
        
        $totalPorEstados = array_reduce($estadosNegocios, function($carry, $item) {
            return $carry + $item['count'];
        }, 0);
        
        Log::info("Total por estados: $totalPorEstados");
        
        if ($totalPorEstados != $totalNegocios) {
            Log::warning("ATENÇÃO: Ainda existem inconsistências! Total ($totalNegocios) != Soma ($totalPorEstados)");
        } else {
            Log::info("Sucesso! Total de negócios = Soma dos estados");
        }
    }
} 