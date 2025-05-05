<?php

namespace App\Http\Controllers;

use App\Models\Atividade;
use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\TipoAtividade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AtividadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $entidade_id = $request->input('entidade_id', '');
        $tipo_id = $request->input('tipo_id', '');
        $data_inicio = $request->input('data_inicio', '');
        $data_fim = $request->input('data_fim', '');
        
        $atividades = Atividade::query()
            ->with(['entidade', 'contacto', 'tipo', 'participantes', 'conhecimento'])
            ->when($search, function ($query, $search) {
                $query->where('descricao', 'like', "%{$search}%")
                      ->orWhereHas('entidade', function ($q) use ($search) {
                          $q->where('nome', 'like', "%{$search}%");
                      })
                      ->orWhereHas('contacto', function ($q) use ($search) {
                          $q->where('nome', 'like', "%{$search}%")
                            ->orWhere('apelido', 'like', "%{$search}%");
                      });
            })
            ->when($entidade_id, function ($query, $entidade_id) {
                $query->where('entidade_id', $entidade_id);
            })
            ->when($tipo_id, function ($query, $tipo_id) {
                $query->where('tipo_id', $tipo_id);
            })
            ->when($data_inicio, function ($query, $data_inicio) {
                $query->whereDate('data', '>=', $data_inicio);
            })
            ->when($data_fim, function ($query, $data_fim) {
                $query->whereDate('data', '<=', $data_fim);
            })
            ->orderBy('data', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        $entidades = Entidade::orderBy('nome')->get(['id', 'nome']);
        $tipos = TipoAtividade::orderBy('nome')->get(['id', 'nome']);
        
        return Inertia::render('atividades/Index', [
            'atividades' => $atividades,
            'entidades' => $entidades,
            'tipos' => $tipos,
            'filters' => [
                'search' => $search,
                'entidade_id' => $entidade_id,
                'tipo_id' => $tipo_id,
                'data_inicio' => $data_inicio,
                'data_fim' => $data_fim,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entidades = Entidade::orderBy('nome')->get(['id', 'nome']);
        $tipos = TipoAtividade::orderBy('nome')->get(['id', 'nome']);
        $users = User::orderBy('name')->get(['id', 'name']);
        
        return Inertia::render('atividades/Create', [
            'entidades' => $entidades,
            'tipos' => $tipos,
            'users' => $users,
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'hora' => 'required',
            'duracao' => 'nullable|integer|min:1',
            'entidade_id' => 'required|exists:entidades,id',
            'contacto_id' => 'nullable|exists:contactos,id',
            'tipo_id' => 'required|exists:tipos_atividade,id',
            'descricao' => 'nullable|string',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id',
            'conhecimento' => 'nullable|array',
            'conhecimento.*' => 'exists:users,id',
        ]);
        
        // Remover arrays de participantes para criar a atividade
        $participantes = $validated['participantes'] ?? [];
        $conhecimento = $validated['conhecimento'] ?? [];
        
        unset($validated['participantes'], $validated['conhecimento']);
        
        // Criar a atividade
        $atividade = Atividade::create($validated);
        
        // Anexar participantes
        if (!empty($participantes)) {
            $atividade->participantes()->attach($participantes);
        }
        
        // Anexar conhecimento
        if (!empty($conhecimento)) {
            $atividade->conhecimento()->attach($conhecimento);
        }
        
        return Redirect::route('atividades.index')
            ->with('success', 'Atividade criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Atividade $atividade)
    {
        $atividade->load(['entidade', 'contacto', 'tipo', 'participantes', 'conhecimento']);
        
        return Inertia::render('atividades/Show', [
            'atividade' => $atividade,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Atividade $atividade)
    {
        $atividade->load(['participantes', 'conhecimento']);
        
        $entidades = Entidade::orderBy('nome')->get(['id', 'nome']);
        $tipos = TipoAtividade::orderBy('nome')->get(['id', 'nome']);
        $users = User::orderBy('name')->get(['id', 'name']);
        
        // Obter contactos da entidade selecionada
        $contactos = [];
        if ($atividade->entidade_id) {
            $contactos = Contacto::where('entidade_id', $atividade->entidade_id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'apelido']);
        }
        
        return Inertia::render('atividades/Edit', [
            'atividade' => $atividade,
            'entidades' => $entidades,
            'tipos' => $tipos,
            'users' => $users,
            'contactos' => $contactos,
            'participantesIds' => $atividade->participantes->pluck('id'),
            'conhecimentoIds' => $atividade->conhecimento->pluck('id'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Atividade $atividade)
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'hora' => 'required',
            'duracao' => 'nullable|integer|min:1',
            'entidade_id' => 'required|exists:entidades,id',
            'contacto_id' => 'nullable|exists:contactos,id',
            'tipo_id' => 'required|exists:tipos_atividade,id',
            'descricao' => 'nullable|string',
            'participantes' => 'nullable|array',
            'participantes.*' => 'exists:users,id',
            'conhecimento' => 'nullable|array',
            'conhecimento.*' => 'exists:users,id',
        ]);
        
        // Remover arrays de participantes para atualizar a atividade
        $participantes = $validated['participantes'] ?? [];
        $conhecimento = $validated['conhecimento'] ?? [];
        
        unset($validated['participantes'], $validated['conhecimento']);
        
        // Atualizar a atividade
        $atividade->update($validated);
        
        // Sincronizar participantes
        $atividade->participantes()->sync($participantes);
        
        // Sincronizar conhecimento
        $atividade->conhecimento()->sync($conhecimento);
        
        return Redirect::route('atividades.show', $atividade)
            ->with('success', 'Atividade atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atividade $atividade)
    {
        $atividade->delete();
        
        return Redirect::route('atividades.index')
            ->with('success', 'Atividade excluída com sucesso.');
    }

    /**
     * Get contacts for a specific entity.
     */
    public function getContacts(Request $request)
    {
        $entidade_id = $request->input('entidade_id');
        
        $contactos = Contacto::where('entidade_id', $entidade_id)
            ->where('estado', 'Ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'apelido']);
        
        return response()->json($contactos);
    }
}
