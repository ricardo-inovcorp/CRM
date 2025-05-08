<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\TipoAtividade;
use App\Models\Entidade;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarioController extends Controller
{
    /**
     * Exibe a página de calendário
     */
    public function index()
    {
        // Buscar todas as atividades para exibir no calendário
        $atividades = Atividade::with(['entidade', 'contacto', 'tipo'])
            ->orderBy('data', 'desc')
            ->orderBy('hora', 'desc')
            ->get();
        
        // Buscar entidades para o formulário de edição
        $entidades = Entidade::orderBy('nome')->get(['id', 'nome']);
        
        // Buscar tipos de atividade para o formulário de edição
        $tipos = TipoAtividade::orderBy('nome')->get(['id', 'nome']);

        return Inertia::render('calendario/Index', [
            'atividades' => $atividades,
            'entidades' => $entidades,
            'tipos' => $tipos
        ]);
    }

    /**
     * Retorna as atividades em formato JSON para o calendário
     */
    public function getActivities()
    {
        $atividades = Atividade::with(['entidade', 'contacto', 'tipo'])
            ->orderBy('data')
            ->orderBy('hora')
            ->get();
        
        return response()->json($atividades);
    }
} 