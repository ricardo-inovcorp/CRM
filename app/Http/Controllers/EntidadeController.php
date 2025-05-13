<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class EntidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $estado = $request->input('estado', '');
        
        $user = Auth::user();
        
        $entidades = Entidade::query()
            ->with('pais')
            // Garantir explicitamente o filtro por tenant para usuários não-admin
            ->when(!$user->is_admin && $user->tenant_id, function ($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id);
            })
            ->when($search, function ($query, $search) {
                $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefone', 'like', "%{$search}%");
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();
        
        return Inertia::render('entidades/Index', [
            'entidades' => $entidades,
            'filters' => [
                'search' => $search,
                'estado' => $estado,
            ],
            'estados_disponiveis' => ['Ativo', 'Inativo']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paises = Pais::orderBy('nome')->get();
        
        return Inertia::render('entidades/Create', [
            'paises' => $paises,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:100',
            'pais_id' => 'nullable|exists:paises,id',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'observacoes' => 'nullable|string',
            'estado' => 'required|string|in:Ativo,Inativo',
        ]);
        
        // Garantir que temos dados válidos para o website
        if (isset($validated['website']) && empty($validated['website'])) {
            $validated['website'] = null;
        }
        
        // O trait BelongsToTenant deve definir o tenant_id automaticamente
        $entidade = Entidade::create($validated);
        
        return Redirect::route('entidades.index')
            ->with('success', 'Entidade criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Entidade $entidade)
    {
        $entidade->load(['pais', 'contactos', 'atividades']);
        
        return Inertia::render('entidades/Show', [
            'entidade' => $entidade,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entidade $entidade)
    {
        $paises = Pais::orderBy('nome')->get();
        
        return Inertia::render('entidades/Edit', [
            'entidade' => $entidade,
            'paises' => $paises,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entidade $entidade)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:100',
            'pais_id' => 'nullable|exists:paises,id',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'observacoes' => 'nullable|string',
            'estado' => 'required|string|in:Ativo,Inativo',
        ]);
        
        // Garantir que temos dados válidos para o website
        if (isset($validated['website']) && empty($validated['website'])) {
            $validated['website'] = null;
        }
        
        $entidade->update($validated);
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Entidade atualizada com sucesso.',
                'entidade' => $entidade->fresh()
            ]);
        }
        
        return back()->with('success', 'Entidade atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entidade $entidade)
    {
        $entidade->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Entidade excluída com sucesso.'
            ]);
        }
        
        return back()->with('success', 'Entidade excluída com sucesso.');
    }
}
