<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Funcao;
use Illuminate\Http\Request;
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
        $estado = $request->input('estado', '');
        $entidade_id = $request->input('entidade_id', '');
        
        $contactos = Contacto::query()
            ->with(['entidade', 'funcao'])
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                      ->orWhere('apelido', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telefone', 'like', "%{$search}%")
                      ->orWhere('telemovel', 'like', "%{$search}%");
                });
            })
            ->when($estado, function ($query, $estado) {
                $query->where('estado', $estado);
            })
            ->when($entidade_id, function ($query, $entidade_id) {
                $query->where('entidade_id', $entidade_id);
            })
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();
        
        $entidades = Entidade::orderBy('nome')->get(['id', 'nome']);
        
        return Inertia::render('contactos/Index', [
            'contactos' => $contactos,
            'entidades' => $entidades,
            'filters' => [
                'search' => $search,
                'estado' => $estado,
                'entidade_id' => $entidade_id,
            ],
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
            'estado' => 'required|string|in:Ativo,Inativo',
        ]);
        
        Contacto::create($validated);
        
        return Redirect::route('contactos.index')
            ->with('success', 'Contacto criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contacto $contacto)
    {
        $contacto->load(['entidade', 'funcao', 'atividades']);
        
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
            'estado' => 'required|string|in:Ativo,Inativo',
        ]);
        
        $contacto->update($validated);
        
        return Redirect::route('contactos.show', $contacto)
            ->with('success', 'Contacto atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contacto $contacto)
    {
        $contacto->delete();
        
        return Redirect::route('contactos.index')
            ->with('success', 'Contacto excluído com sucesso.');
    }
}
