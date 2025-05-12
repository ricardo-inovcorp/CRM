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
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;

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
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');
    
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

    // Rotas CRM
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

    // Direct test route for users page
    Route::get('/users-test', function () {
        $users = \App\Models\User::with('roles')->where('tenant_id', \Illuminate\Support\Facades\Auth::user()->tenant_id)->get();
        $roles = \App\Models\Role::all();
        
        \Illuminate\Support\Facades\Log::info('Direct test route - Users count: ' . $users->count());
        
        return \Inertia\Inertia::render('users/Index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    })->name('users.test');

    // Rotas de gestão de usuários (sem middleware de verificação de role)
    Route::resource('users', UserController::class);

    // Direct route to UserController for debugging
    Route::get('/users-debug', [UserController::class, 'debug'])->name('users.debug');

    // Debug route to check middleware
    Route::get('/users-middleware', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        return response()->json([
            'message' => 'Middleware check passed',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'is_admin' => $user->isAdmin(),
            'roles' => $user->roles->pluck('slug'),
        ]);
    })->name('users.middleware');

    // Route info debug - does not require auth
    Route::get('/routes-debug', function () {
        return response()->json([
            'routes' => [
                'users.index' => route('users.index'),
                'users.create' => route('users.create'),
                'users.edit' => route('users.edit', ['user' => 1]),
                'users.show' => route('users.show', ['user' => 1]),
                'users.destroy' => route('users.destroy', ['user' => 1]),
                'users.test' => route('users.test'),
                'users.debug' => route('users.debug'),
                'users.middleware' => route('users.middleware'),
            ],
        ]);
    })->name('routes.debug');

    // Most basic test route for debugging
    Route::get('/simple-debug', function () {
        $users = \App\Models\User::with('roles')->where('tenant_id', \Illuminate\Support\Facades\Auth::user()->tenant_id)->get();
        $roles = \App\Models\Role::all();
        
        // Usar lowercase em vez de uppercase
        return \Inertia\Inertia::render('users/Debug', [
            'users' => $users,
            'roles' => $roles,
        ]);
    })->name('users.simple-debug');
    
    // Try the lowercase path version
    Route::get('/simple-debug-lowercase', function () {
        $users = \App\Models\User::with('roles')->where('tenant_id', \Illuminate\Support\Facades\Auth::user()->tenant_id)->get();
        $roles = \App\Models\Role::all();
        
        return \Inertia\Inertia::render('users/Debug', [
            'users' => $users,
            'roles' => $roles,
        ]);
    })->name('users.simple-debug-lowercase');

    // Direct test with minimal components
    Route::get('/direct-test', function () {
        return \Inertia\Inertia::render('DirectTest');
    })->name('direct.test');

    // Direct users route without controller
    Route::get('/users-direct', function () {
        $users = \App\Models\User::with('roles')->where('tenant_id', \Illuminate\Support\Facades\Auth::user()->tenant_id)->get();
        $roles = \App\Models\Role::all();
        
        return \Inertia\Inertia::render('users/Index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    })->name('users.direct');

    // Direct route with no role checks - for testing only
    Route::get('/users-bypass', function () {
        $users = \App\Models\User::with('roles')->get(); // Get all users without tenant filtering
        $roles = \App\Models\Role::all();
        
        return \Inertia\Inertia::render('users/Index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    })->name('users.bypass');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
