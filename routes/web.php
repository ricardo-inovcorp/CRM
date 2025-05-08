<?php

use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\SettingsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FuncaoController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\TipoAtividadeController;
use App\Http\Controllers\NegocioController;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Illuminate\Foundation\Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    })->name('home');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Calendário
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/api/atividades/calendar', [CalendarioController::class, 'getActivities'])->name('api.atividades.calendar');
    
    // Rota de teste para entidades
    Route::get('entidades-test', function () {
        return Inertia::render('EntidadesTest');
    })->name('entidades.test');

    // Relatórios
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/entidades/pdf', [RelatorioController::class, 'entidadesPdf'])->name('relatorios.entidades.pdf');
    Route::get('/relatorios/contactos/pdf', [RelatorioController::class, 'contactosPdf'])->name('relatorios.contactos.pdf');
    Route::get('/relatorios/atividades/pdf', [RelatorioController::class, 'atividadesPdf'])->name('relatorios.atividades.pdf');
    Route::get('/relatorios/atividades-por-entidade/pdf', [RelatorioController::class, 'atividadesPorEntidadePdf'])->name('relatorios.atividades-por-entidade.pdf');

    // Rota de teste para diagnóstico
    Route::get('/teste-erro', function () {
        return Inertia::render('ErrorTest');
    })->name('error.test');

    // Rotas CRM (removendo temporariamente o middleware tenant)
    // Entidades
    Route::resource('entidades', EntidadeController::class);
    
    // Contactos
    Route::resource('contactos', ContactoController::class);
    
    // Atividades
    Route::get('atividades/get-contacts', [AtividadeController::class, 'getContacts'])->name('atividades.getContacts');
    Route::resource('atividades', AtividadeController::class);
    
    // Rota para buscar contactos de uma entidade
    Route::get('/api/entidades/{entidade}/contactos', [ContactoController::class, 'getContactosByEntidade']);
    
    // Configurações
    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        // Países
        Route::resource('paises', PaisController::class);
        
        // Funções
        Route::resource('funcoes', FuncaoController::class);
        
        // Tipos de Atividade
        Route::resource('tipos-atividade', TipoAtividadeController::class);
    });

    // Negócios
    Route::resource('negocios', NegocioController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
