<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Negocio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Mostra o dashboard com estatísticas iniciais.
     */
    public function index(Request $request): Response
    {
        // Tenant atual é automaticamente filtrado pelos global scopes dos models

        // Totais das entidades principais
        $totalEntidades = Entidade::count();
        $totalContactos = Contacto::count();
        $totalAtividades = Atividade::count();
        
        // Verificar todos os negócios para debug
        $todosNegocios = Negocio::select('id', 'nome', 'estado')->get();
        \Illuminate\Support\Facades\Log::info('Todos os negócios:', $todosNegocios->toArray());
        
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
        
        \Illuminate\Support\Facades\Log::info('Estados dos negócios:', [
            'estadosNegocios' => $estadosNegocios,
            'totalNegocios' => $totalNegocios
        ]);

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
                    'valor' => (float)$negocio->valor,
                    'entidade' => $negocio->entidade ? $negocio->entidade->nome : 'N/A'
                ];
            });

        // Retorna a vista com os dados iniciais
        return Inertia::render('Dashboard', [
            'initialStats' => [
            'totalEntidades' => $totalEntidades,
            'totalContactos' => $totalContactos,
                'totalAtividades' => $totalAtividades,
                'totalNegocios' => $totalNegocios,
                'valorTotalNegocios' => (float)$valorTotalNegocios,
                'valorTotalNegociosGanhos' => (float)$valorTotalNegociosGanhos,
                'estadosNegocios' => $estadosNegocios,
            'atividadesRecentes' => $atividadesRecentes,
                'negociosRecentes' => $negociosRecentes
            ]
        ]);
    }
} 