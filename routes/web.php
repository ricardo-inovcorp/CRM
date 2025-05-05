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
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rotas CRM protegidas por Tenant
    Route::middleware(['ensure.tenant.exists'])->group(function () {
        // Entidades
        Route::resource('entidades', EntidadeController::class);
        
        // Contactos
        Route::resource('contactos', ContactoController::class);
        
        // Atividades
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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
