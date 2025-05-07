<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Contacto;
use App\Models\Entidade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    /**
     * Exibe a página principal de relatórios
     */
    public function index()
    {
        // Estatísticas para o dashboard de relatórios
        $estatisticas = [
            'total_entidades' => Entidade::count(),
            'total_contactos' => Contacto::count(),
            'total_atividades' => Atividade::count(),
            'entidades_ativas' => Entidade::where('estado', 'Ativo')->count(),
            'contactos_ativos' => Contacto::where('estado', 'Ativo')->count(),
            'atividades_recentes' => Atividade::whereDate('data', '>=', Carbon::now()->subDays(30))->count(),
        ];

        // Tipos de relatórios disponíveis
        $tipos_relatorios = [
            [
                'id' => 'entidades', 
                'titulo' => 'Relatório de Entidades',
                'descricao' => 'Lista de todas as entidades com seus dados principais e estatísticas.'
            ],
            [
                'id' => 'contactos', 
                'titulo' => 'Relatório de Contactos',
                'descricao' => 'Lista de todos os contactos agrupados por entidade.'
            ],
            [
                'id' => 'atividades', 
                'titulo' => 'Relatório de Atividades',
                'descricao' => 'Histórico de atividades com data, tipo e entidade.'
            ],
            [
                'id' => 'atividades_por_entidade', 
                'titulo' => 'Atividades por Entidade',
                'descricao' => 'Análise das atividades agrupadas por entidade.'
            ],
        ];

        return Inertia::render('relatorios/Index', [
            'estatisticas' => $estatisticas,
            'tipos_relatorios' => $tipos_relatorios
        ]);
    }

    /**
     * Gera e retorna o relatório de entidades em PDF
     */
    public function entidadesPdf(Request $request)
    {
        $filtro_estado = $request->input('estado', 'todos');
        
        $query = Entidade::with(['contactos', 'atividades'])
            ->withCount(['contactos', 'atividades']);
        
        if ($filtro_estado !== 'todos') {
            $query->where('estado', $filtro_estado);
        }
        
        $entidades = $query->get();
        
        $data = [
            'titulo' => 'Relatório de Entidades',
            'data_geracao' => Carbon::now()->format('d/m/Y H:i'),
            'entidades' => $entidades,
            'filtros' => [
                'estado' => $filtro_estado
            ],
            'total' => $entidades->count()
        ];
        
        $pdf = Pdf::loadView('pdf.entidades', $data);
        
        return $pdf->download('relatorio_entidades_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Gera e retorna o relatório de contactos em PDF
     */
    public function contactosPdf(Request $request)
    {
        $filtro_estado = $request->input('estado', 'todos');
        $filtro_entidade = $request->input('entidade_id', null);
        
        $query = Contacto::with('entidade')->withCount('atividades');
        
        if ($filtro_estado !== 'todos') {
            $query->where('estado', $filtro_estado);
        }
        
        if ($filtro_entidade) {
            $query->where('entidade_id', $filtro_entidade);
        }
        
        $contactos = $query->get();
        
        $data = [
            'titulo' => 'Relatório de Contactos',
            'data_geracao' => Carbon::now()->format('d/m/Y H:i'),
            'contactos' => $contactos,
            'filtros' => [
                'estado' => $filtro_estado,
                'entidade_id' => $filtro_entidade
            ],
            'total' => $contactos->count()
        ];
        
        $pdf = Pdf::loadView('pdf.contactos', $data);
        
        return $pdf->download('relatorio_contactos_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Gera e retorna o relatório de atividades em PDF
     */
    public function atividadesPdf(Request $request)
    {
        $filtro_data_inicio = $request->input('data_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $filtro_data_fim = $request->input('data_fim', Carbon::now()->format('Y-m-d'));
        $filtro_entidade = $request->input('entidade_id', null);
        $filtro_tipo = $request->input('tipo_id', null);
        
        $query = Atividade::with(['entidade', 'contacto', 'tipo'])
            ->whereDate('data', '>=', $filtro_data_inicio)
            ->whereDate('data', '<=', $filtro_data_fim);
        
        if ($filtro_entidade) {
            $query->where('entidade_id', $filtro_entidade);
        }
        
        if ($filtro_tipo) {
            $query->where('tipo_id', $filtro_tipo);
        }
        
        $atividades = $query->orderBy('data', 'desc')
            ->orderBy('hora', 'desc')
            ->get();
        
        $data = [
            'titulo' => 'Relatório de Atividades',
            'data_geracao' => Carbon::now()->format('d/m/Y H:i'),
            'atividades' => $atividades,
            'filtros' => [
                'data_inicio' => $filtro_data_inicio,
                'data_fim' => $filtro_data_fim,
                'entidade_id' => $filtro_entidade,
                'tipo_id' => $filtro_tipo
            ],
            'total' => $atividades->count()
        ];
        
        $pdf = Pdf::loadView('pdf.atividades', $data);
        
        return $pdf->download('relatorio_atividades_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Gera e retorna o relatório de atividades por entidade em PDF
     */
    public function atividadesPorEntidadePdf(Request $request)
    {
        $filtro_data_inicio = $request->input('data_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $filtro_data_fim = $request->input('data_fim', Carbon::now()->format('Y-m-d'));
        
        $atividades_por_entidade = DB::table('atividades')
            ->join('entidades', 'atividades.entidade_id', '=', 'entidades.id')
            ->select('entidades.id', 'entidades.nome')
            ->selectRaw('COUNT(*) as total_atividades')
            ->selectRaw('SUM(duracao) as duracao_total')
            ->whereDate('atividades.data', '>=', $filtro_data_inicio)
            ->whereDate('atividades.data', '<=', $filtro_data_fim)
            ->groupBy('entidades.id', 'entidades.nome')
            ->orderByDesc('total_atividades')
            ->get();
        
        $data = [
            'titulo' => 'Relatório de Atividades por Entidade',
            'data_geracao' => Carbon::now()->format('d/m/Y H:i'),
            'atividades_por_entidade' => $atividades_por_entidade,
            'filtros' => [
                'data_inicio' => $filtro_data_inicio,
                'data_fim' => $filtro_data_fim
            ],
            'total_entidades' => $atividades_por_entidade->count()
        ];
        
        $pdf = Pdf::loadView('pdf.atividades_por_entidade', $data);
        
        return $pdf->download('relatorio_atividades_por_entidade_' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
} 