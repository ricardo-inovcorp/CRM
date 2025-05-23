<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\TipoNegocio;
use App\Models\Entidade;
use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class NegocioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $tipo_id = $request->input('tipo_id', '');
        $entidade_id = $request->input('entidade_id', '');
        $estado = $request->input('estado', '');
        
        // Usar eager loading com queries mais específicas
        $negocios = Negocio::with([
            'tipo', 
            'entidade',
            'contactos' => function($query) {
                // Usar o qualificador da tabela para evitar ambiguidade
                $query->select('contactos.*')
                      ->where(function($q) {
                          $q->where('contactos.tenant_id', Auth::user()->tenant_id)
                            ->orWhereNull('contactos.tenant_id');
                      });
            }
        ])
        ->when($search, function ($query, $search) {
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhereHas('entidade', function ($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  });
        })
        ->when($tipo_id, function ($query, $tipo_id) {
            $query->where('tipo_id', $tipo_id);
        })
        ->when($entidade_id, function ($query, $entidade_id) {
            $query->where('entidade_id', $entidade_id);
        })
        ->when($estado, function ($query, $estado) {
            $query->where('estado', $estado);
        })
        ->paginate(15)
        ->withQueryString();
        
        // Log para debug
        Log::info('Listando negócios', [
            'total' => $negocios->total(),
            'tenant_id' => Auth::user()->tenant_id,
            'ids' => $negocios->items() ? collect($negocios->items())->pluck('id') : []
        ]);
        
        $tipos = TipoNegocio::orderBy('nome')->get();
        $entidades = Entidade::orderBy('nome')->get();
        $contactos = Contacto::orderBy('nome')->get();
        $estados = Negocio::ESTADOS;
        return Inertia::render('negocios/Index', [
            'negocios' => $negocios,
            'tipos' => $tipos,
            'entidades' => $entidades,
            'contactos' => $contactos,
            'estados' => $estados,
            'filters' => [
                'search' => $search,
                'tipo_id' => $tipo_id,
                'entidade_id' => $entidade_id,
                'estado' => $estado,
            ],
        ]);
    }

    public function create()
    {
        $tipos = TipoNegocio::orderBy('nome')->get();
        $entidades = Entidade::orderBy('nome')->get();
        $contactos = Contacto::orderBy('nome')->get();
        $estados = Negocio::ESTADOS;
        return Inertia::render('negocios/Create', [
            'tipos' => $tipos,
            'entidades' => $entidades,
            'contactos' => $contactos,
            'estados' => $estados,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_id' => 'required|exists:tipos_negocio,id',
            'entidade_id' => 'required|exists:entidades,id',
            'valor' => 'nullable|numeric',
            'estado' => 'required|in:' . implode(',', Negocio::ESTADOS),
            'contactos' => 'array',
            'contactos.*' => 'exists:contactos,id',
        ]);

        // Adicionar o tenant_id explicitamente
        $validated['tenant_id'] = Auth::user()->tenant_id;
        
        // Log para debug
        Log::info('Criando negócio', [
            'dados' => $validated,
            'usuario' => Auth::user()->id,
            'tenant' => Auth::user()->tenant_id
        ]);

        $negocio = Negocio::create($validated);
        
        $dadosCompletos = $validated;
        
        if (!empty($validated['contactos'])) {
            $negocio->contactos()->sync($validated['contactos']);
            $dadosCompletos['contactos'] = $validated['contactos'];
        }
        
        // Registrar log de criação
        $negocio->registrarLog(
            'criacao',
            'Negócio criado por ' . Auth::user()->name,
            null,
            $dadosCompletos
        );

        return Redirect::route('negocios.index')->with('success', 'Negócio criado com sucesso.');
    }

    public function show(Negocio $negocio)
    {
        $negocio->load([
            'tipo', 
            'entidade',
            'contactos' => function($query) {
                $query->select('contactos.*')
                      ->where(function($q) {
                          $q->where('contactos.tenant_id', Auth::user()->tenant_id)
                            ->orWhereNull('contactos.tenant_id');
                      });
            },
            'logs.user'
        ]);
        
        return Inertia::render('negocios/Show', [
            'negocio' => $negocio,
        ]);
    }

    public function edit(Negocio $negocio)
    {
        $tipos = TipoNegocio::orderBy('nome')->get();
        $entidades = Entidade::orderBy('nome')->get();
        $contactos = Contacto::orderBy('nome')->get();
        $estados = Negocio::ESTADOS;
        
        $negocio->load([
            'contactos' => function($query) {
                $query->select('contactos.*')
                      ->where(function($q) {
                          $q->where('contactos.tenant_id', Auth::user()->tenant_id)
                            ->orWhereNull('contactos.tenant_id');
                      });
            }
        ]);
        
        return Inertia::render('negocios/Edit', [
            'negocio' => $negocio,
            'tipos' => $tipos,
            'entidades' => $entidades,
            'contactos' => $contactos,
            'estados' => $estados,
        ]);
    }

    public function update(Request $request, Negocio $negocio)
    {
        // Se for uma requisição AJAX só para atualizar o estado (Kanban)
        if ($request->wantsJson() || $request->ajax() || $request->isJson() || $request->has('estado') && count($request->all()) === 1) {
            $validated = $request->validate([
                'estado' => 'required|in:' . implode(',', Negocio::ESTADOS),
            ]);
            
            // Capturar dados anteriores
            $dadosAnteriores = $negocio->toArray();
            
            $negocio->update($validated);
            
            // Registrar log de alteração de estado
            $negocio->registrarLog(
                'alteracao',
                'Estado do negócio alterado por ' . Auth::user()->name,
                ['estado' => $dadosAnteriores['estado']],
                ['estado' => $validated['estado']]
            );
            
            // Verificar se a requisição espera uma resposta Inertia (do formulário modal)
            if ($request->header('X-Inertia')) {
                return Redirect::route('negocios.index');
            }
            
            // Caso contrário, retornar JSON para requisições de API (kanban)
            return response()->json(['success' => true, 'negocio' => $negocio->fresh()]);
        }

        // Validação completa para update tradicional
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_id' => 'required|exists:tipos_negocio,id',
            'entidade_id' => 'required|exists:entidades,id',
            'valor' => 'nullable|numeric',
            'estado' => 'required|in:' . implode(',', Negocio::ESTADOS),
            'contactos' => 'array',
            'contactos.*' => 'exists:contactos,id',
        ]);

        // Capturar dados anteriores para o log
        $dadosAnteriores = $negocio->toArray();
        $dadosAnteriores['contactos'] = $negocio->contactos()->pluck('contactos.id')->toArray();
        
        $negocio->update($validated);
        
        $dadosNovos = $validated;
        
        if (isset($validated['contactos'])) {
            $negocio->contactos()->sync($validated['contactos']);
            $dadosNovos['contactos'] = $validated['contactos'];
        }
        
        // Registrar log de alteração apenas se houve mudanças
        if ($dadosAnteriores != array_merge($negocio->toArray(), ['contactos' => $dadosNovos['contactos'] ?? []])) {
            $negocio->registrarLog(
                'alteracao',
                'Negócio alterado por ' . Auth::user()->name,
                $dadosAnteriores,
                $dadosNovos
            );
        }

        return Redirect::route('negocios.index')->with('success', 'Negócio atualizado com sucesso.');
    }

    public function destroy(Negocio $negocio)
    {
        $negocio->delete();
        return Redirect::route('negocios.index')->with('success', 'Negócio removido com sucesso.');
    }
} 