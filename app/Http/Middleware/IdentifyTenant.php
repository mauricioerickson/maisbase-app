<?php

// filepath: app/Http/Middleware/IdentifyTenant.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware para identificar o Tenant do utilizador autenticado.
 * Persiste o tenant_id na sessão para uso global.
 */
class IdentifyTenant
{
    /**
     * Manipula a requisição de entrada.
     */
    public function handle(Request $request, Closure $next)
    {
        // Se o utilizador estiver logado, captura o tenant_id do seu perfil
        if (Auth::check()) {
            $tenantId = Auth::user()->tenant_id;
            
            if ($tenantId) {
                // Persiste na sessão para que o GlobalScope da Trait funcione
                session(['tenant_id' => $tenantId]);
            }
        }

        return $next($request);
    }
}
