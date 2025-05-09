<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado e tem um tenant_id
        if (!Auth::check() || !Auth::user()->tenant_id) {
            abort(403, 'Você não está associado a nenhuma organização.');
        }
        
        // Define a variável global para o tenant_id atual
        app()->instance('tenant_id', Auth::user()->tenant_id);
        
        return $next($request);
    }
} 