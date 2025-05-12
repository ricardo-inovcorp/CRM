<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Constructor to diagnose middleware issues
     */
    public function __construct()
    {
        Log::info('UserController constructor called');
    }

    /**
     * Mostrar a lista de usuários do tenant atual.
     */
    public function index()
    {
        // Obter apenas usuários do tenant atual (o filtro já é aplicado pelo Global Scope)
        $users = User::with('roles')->where('tenant_id', Auth::user()->tenant_id)->get();
        $roles = Role::all();
        
        // Log para debugging
        Log::info('UserController::index - Rendering users page');
        Log::info('UserController::index - Users count: ' . $users->count());
        Log::info('UserController::index - Roles count: ' . $roles->count());
        
        // Usar o caminho com letra minúscula
        return Inertia::render('users/Index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Mostrar o formulário para criar um novo usuário.
     */
    public function create()
    {
        $roles = Role::all();
        
        return Inertia::render('users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Armazenar um novo usuário.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => 'required|array|size:1',
            'roles.*' => 'exists:roles,id',
        ]);
        
        // Criar o usuário associado ao tenant atual
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => Auth::user()->tenant_id, // Associar ao tenant atual
        ]);
        
        // Adicionar a role selecionada
        $user->roles()->sync($request->roles);
        
        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Mostrar um usuário específico.
     */
    public function show(User $user)
    {
        // Verificar se o usuário pertence ao tenant atual
        if ($user->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Acesso negado.');
        }
        
        $user->load('roles');
        
        return Inertia::render('users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Mostrar o formulário para editar um usuário.
     */
    public function edit(User $user)
    {
        // Verificar se o usuário pertence ao tenant atual
        if ($user->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Acesso negado.');
        }
        
        $user->load('roles');
        $roles = Role::all();
        
        return Inertia::render('users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $user->roles->pluck('id'),
        ]);
    }

    /**
     * Atualizar um usuário.
     */
    public function update(Request $request, User $user)
    {
        // Verificar se o usuário pertence ao tenant atual
        if ($user->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Acesso negado.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => 'required|array|size:1',
            'roles.*' => 'exists:roles,id',
        ]);
        
        // Atualizar dados do usuário
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        // Atualizar a role
        $user->roles()->sync($request->roles);
        
        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remover um usuário.
     */
    public function destroy(User $user)
    {
        // Verificar se o usuário pertence ao tenant atual
        if ($user->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Acesso negado.');
        }
        
        // Não permitir que o admin exclua a si mesmo
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Você não pode excluir seu próprio usuário.');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')->with('success', 'Usuário removido com sucesso!');
    }

    /**
     * Debug method to diagnose routing issues.
     */
    public function debug()
    {
        // Log diagnostic information
        Log::info('UserController::debug - Reached debug method');
        
        $users = User::with('roles')->where('tenant_id', Auth::user()->tenant_id)->get();
        $roles = Role::all();
        
        Log::info('UserController::debug - Users count: ' . $users->count());
        Log::info('UserController::debug - Roles count: ' . $roles->count());
        
        // Return JSON for direct debugging
        return response()->json([
            'message' => 'Debug route reached successfully',
            'users_count' => $users->count(),
            'roles_count' => $roles->count(),
            'can_view_users_index' => route('users.index'),
        ]);
    }
}
