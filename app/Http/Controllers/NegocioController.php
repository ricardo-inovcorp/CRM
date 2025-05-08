<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\TipoNegocio;
use App\Models\Entidade;
use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class NegocioController extends Controller
{
    public function index()
    {
        $negocios = Negocio::with(['tipo', 'entidade', 'contactos'])->paginate(15);
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

        $negocio = Negocio::create($validated);
        if (!empty($validated['contactos'])) {
            $negocio->contactos()->sync($validated['contactos']);
        }

        return Redirect::route('negocios.index')->with('success', 'Negócio criado com sucesso.');
    }

    public function show(Negocio $negocio)
    {
        $negocio->load(['tipo', 'entidade', 'contactos']);
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
        $negocio->load('contactos');
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
            $negocio->update($validated);
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

        $negocio->update($validated);
        if (isset($validated['contactos'])) {
            $negocio->contactos()->sync($validated['contactos']);
        }

        return Redirect::route('negocios.index')->with('success', 'Negócio atualizado com sucesso.');
    }

    public function destroy(Negocio $negocio)
    {
        $negocio->delete();
        return Redirect::route('negocios.index')->with('success', 'Negócio removido com sucesso.');
    }
} 