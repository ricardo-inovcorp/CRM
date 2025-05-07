<?php

use App\Http\Controllers\AtividadeController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\FuncaoController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\TipoAtividadeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Illuminate\Foundation\Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rota de teste para entidades
    Route::get('entidades-test', function () {
        return Inertia::render('EntidadesTest');
    })->name('entidades.test');

    // Relatórios
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [App\Http\Controllers\RelatorioController::class, 'index'])->name('index');
        Route::get('/entidades/pdf', [App\Http\Controllers\RelatorioController::class, 'entidadesPdf'])->name('entidades.pdf');
        Route::get('/contactos/pdf', [App\Http\Controllers\RelatorioController::class, 'contactosPdf'])->name('contactos.pdf');
        Route::get('/atividades/pdf', [App\Http\Controllers\RelatorioController::class, 'atividadesPdf'])->name('atividades.pdf');
        Route::get('/atividades-por-entidade/pdf', [App\Http\Controllers\RelatorioController::class, 'atividadesPorEntidadePdf'])->name('atividades-por-entidade.pdf');
    });

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
    
    // Configurações
    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        // Países
        Route::resource('paises', PaisController::class);
        
        // Funções
        Route::resource('funcoes', FuncaoController::class);
        
        // Tipos de Atividade
        Route::resource('tipos-atividade', TipoAtividadeController::class);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
