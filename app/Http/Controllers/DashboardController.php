<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Contacto;
use App\Models\Entidade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Exibe a página principal do dashboard
     */
    public function index()
    {
        // Contar entidades, contactos e atividades
        $totalEntidades = Entidade::count();
        $totalContactos = Contacto::count();
        
        // Atividades recentes (últimos 30 dias)
        $atividadesRecentes = Atividade::with(['entidade', 'tipo'])
            ->whereDate('data', '>=', Carbon::now()->subDays(30))
            ->orderBy('data', 'desc')
            ->orderBy('hora', 'desc')
            ->take(5)
            ->get();
        
        // Próximas atividades (a partir de hoje)
        $proximasAtividades = Atividade::with(['entidade', 'tipo'])
            ->whereDate('data', '>=', Carbon::today())
            ->orderBy('data', 'asc')
            ->orderBy('hora', 'asc')
            ->take(5)
            ->get();
            
        return Inertia::render('Dashboard', [
            'totalEntidades' => $totalEntidades,
            'totalContactos' => $totalContactos,
            'atividadesRecentes' => $atividadesRecentes,
            'proximasAtividades' => $proximasAtividades
        ]);
    }
} 