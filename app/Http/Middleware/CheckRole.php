<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user() || !$request->user()->hasRole($roles)) {
            // Se não tiver permissão, redireciona ou retorna erro 403
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Não autorizado.'], 403);
            }

            abort(403, 'Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
