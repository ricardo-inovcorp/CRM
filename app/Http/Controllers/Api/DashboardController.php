<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Atividade;
use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Negocio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retorna as estatísticas para o dashboard
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function stats(Request $request): JsonResponse
    {
        // Tenant atual é automaticamente filtrado pelos global scopes dos models

        // Totais das entidades principais
        $totalEntidades = Entidade::count();
        $totalContactos = Contacto::count();
        $totalAtividades = Atividade::count();
        
        // Obter todos os negócios para contagem correta
        $todosNegocios = Negocio::select('id', 'nome', 'estado')->get();
        $totalNegocios = $todosNegocios->count();

        // Valores de negócios
        $valorTotalNegocios = Negocio::sum('valor');
        $valorTotalNegociosGanhos = Negocio::where('estado', 'ganho')->sum('valor');

        // Negócios por estado - abordagem mais confiável usando o modelo Eloquent
        $negociosPorEstadoCollection = Negocio::select('estado')
            ->where('estado', '!=', '')
            ->whereNotNull('estado')
            ->get()
            ->groupBy('estado');
            
        $estadosNegocios = [];
        foreach ($negociosPorEstadoCollection as $estado => $negocios) {
            $estadosNegocios[] = (object)[
                'estado' => $estado,
                'count' => $negocios->count()
            ];
        }

        // Verificar se os totais batem
        $totalPorEstados = array_reduce($estadosNegocios, function($carry, $item) {
            return $carry + $item->count;
        }, 0);

        // Se houver inconsistência, usar o total de negócios como referência
        if ($totalPorEstados != $totalNegocios) {
            \Illuminate\Support\Facades\Log::warning(
                "Inconsistência na contagem de negócios: Total ($totalNegocios) != Soma por estados ($totalPorEstados)"
            );
        }

        // Atividades recentes (últimas 3)
        $atividadesRecentes = Atividade::with(['entidade', 'tipo'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($atividade) {
                return [
                    'id' => $atividade->id,
                    'descricao' => $atividade->descricao,
                    'data' => $atividade->data ? $atividade->data->format('Y-m-d') : null,
                    'tipo' => $atividade->tipo ? $atividade->tipo->nome : 'N/A',
                    'entidade' => $atividade->entidade ? $atividade->entidade->nome : 'N/A'
                ];
            });

        // Negócios recentes (últimos 3)
        $negociosRecentes = Negocio::with('entidade')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($negocio) {
                return [
                    'id' => $negocio->id,
                    'nome' => $negocio->nome,
                    'estado' => $negocio->estado,
                    'valor' => $negocio->valor,
                    'entidade' => $negocio->entidade ? $negocio->entidade->nome : 'N/A'
                ];
            });

        return response()->json([
            'totalEntidades' => $totalEntidades,
            'totalContactos' => $totalContactos,
            'totalAtividades' => $totalAtividades,
            'totalNegocios' => $totalNegocios,
            'valorTotalNegocios' => $valorTotalNegocios,
            'valorTotalNegociosGanhos' => $valorTotalNegociosGanhos,
            'estadosNegocios' => $estadosNegocios,
            'atividadesRecentes' => $atividadesRecentes,
            'negociosRecentes' => $negociosRecentes
        ]);
    }
} 