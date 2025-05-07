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

    // Relatórios (rota temporária)
    Route::get('relatorios', function () {
        return Inertia::render('Dashboard', ['message' => 'Relatórios em desenvolvimento']);
    })->name('relatorios');

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
