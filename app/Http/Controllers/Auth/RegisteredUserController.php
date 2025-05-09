<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nome_empresa' => 'required|string|max:255',
            'nif' => 'nullable|string|max:50',
            'morada' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:50',
        ]);

        // Criar o tenant
        $tenant = Tenant::create([
            'nome' => $request->input('nome_empresa'),
            'nif' => $request->input('nif'),
            'morada' => $request->input('morada'),
            'telefone' => $request->input('telefone'),
        ]);

        // Criar o utilizador associado ao tenant
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id,
        ]);

        // Associar a role de Admin ao primeiro usuário
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $user->roles()->attach($adminRole);
        }

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
