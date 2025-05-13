<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Funcao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $entidade_id = $request->input('entidade_id', '');
        $estado = $request->input('estado', '');
        
        $user = Auth::user();
        
        $contactos = Contacto::query()
            ->with(['entidade', 'funcao'])
            // Garantir explicitamente o filtro por tenant para usuários não-admin
            ->when(!$user->is_admin && $user->tenant_id, function ($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('apelido', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('telefone', 'like', "%{$search}%")
                        ->orWhere('telemovel', 'like', "%{$search}%")
                        ->orWhereHas('entidade', function ($q) use ($search) {
                            $q->where('nome', 'like', "%{$search}%");
                        });
                });
            })
            ->when($entidade_id, function ($query, $entidade_id) {
                $query->where('entidade_id', $entidade_id);
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->orderBy('nome')
            ->orderBy('apelido')
            ->paginate(10)
            ->withQueryString();
        
        // Filtrar as entidades pelo tenant do usuário também
        $entidades = Entidade::orderBy('nome')->get(['id', 'nome']);
        
        return Inertia::render('contactos/Index', [
            'contactos' => $contactos,
            'entidades' => $entidades,
            'filters' => [
                'search' => $search,
                'entidade_id' => $entidade_id,
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
        $entidades = Entidade::orderBy('nome')->get(['id', 'nome']);
        $funcoes = Funcao::orderBy('nome')->get(['id', 'nome']);
        
        return Inertia::render('contactos/Create', [
            'entidades' => $entidades,
            'funcoes' => $funcoes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'entidade_id' => 'required|exists:entidades,id',
            'funcao_id' => 'nullable|exists:funcoes,id',
            'telefone' => 'nullable|string|max:20',
            'telemovel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
            'estado' => 'required|string|in:' . implode(',', Contacto::ESTADOS_DISPONIVEIS),
        ]);
        
        $contacto = Contacto::create($validated);
        
        // Registrar log de criação
        $contacto->registrarLog(
            'criacao',
            'Contacto criado por ' . Auth::user()->name,
            null,
            $validated
        );
        
        return Redirect::route('contactos.index')
            ->with('success', 'Contacto criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contacto $contacto)
    {
        $contacto->load(['entidade', 'funcao', 'atividades', 'logs.user']);
        
        return Inertia::render('contactos/Show', [
            'contacto' => $contacto,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contacto $contacto)
    {
        $entidades = Entidade::orderBy('nome')->get(['id', 'nome']);
        $funcoes = Funcao::orderBy('nome')->get(['id', 'nome']);
        
        return Inertia::render('contactos/Edit', [
            'contacto' => $contacto,
            'entidades' => $entidades,
            'funcoes' => $funcoes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contacto $contacto)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'entidade_id' => 'required|exists:entidades,id',
            'funcao_id' => 'nullable|exists:funcoes,id',
            'telefone' => 'nullable|string|max:20',
            'telemovel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
            'estado' => 'required|string|in:' . implode(',', Contacto::ESTADOS_DISPONIVEIS),
        ]);
        
        // Capturar os dados anteriores para o log
        $dadosAnteriores = $contacto->toArray();
        
        // Atualizar o contacto
        $contacto->update($validated);
        
        // Registrar log de alteração apenas se houve alterações
        if ($dadosAnteriores != $contacto->toArray()) {
            $contacto->registrarLog(
                'alteracao',
                'Contacto alterado por ' . Auth::user()->name,
                $dadosAnteriores,
                $validated
            );
        }
        
        return back()->with('success', 'Contacto atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contacto $contacto)
    {
        $contacto->delete();
        
        return back()->with('success', 'Contacto excluído com sucesso.');
    }
}
