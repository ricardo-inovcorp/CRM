<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Exceptions\NoCurrentTenant;
use Spatie\Multitenancy\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Comentando temporariamente esta verificação para permitir acesso
            // if (! Tenant::checkCurrent()) {
            //     return redirect('/');
            // }
            return $next($request);
        } catch (NoCurrentTenant) {
            return redirect('/');
        }
    }
}
