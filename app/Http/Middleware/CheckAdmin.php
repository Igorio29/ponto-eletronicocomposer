<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para restringir o acesso apenas a usuários com nivel_acesso = 2 (Admin).
 */
class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está logado e se o nível de acesso é 2
        if ($request->user() && $request->user()->nivel_acesso == 2) {
            return $next($request);
        }

        // Caso contrário, bloqueia o acesso com erro 403 (Proibido)
        abort(403, 'Acesso negado. Você não tem permissão para acessar esta página.');
    }
}
