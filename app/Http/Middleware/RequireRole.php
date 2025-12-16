<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireRole
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('role:Administrador,Cocinero')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $session = $request->session();

        // Determine role: prefer empleado role if present, else if usuario is logged treat as 'Usuario'
        $role = null;
        if ($session->has('empleado_cargo')) {
            $role = $session->get('empleado_cargo');
        } elseif ($session->has('usuario_nombre')) {
            $role = 'Usuario';
        }

        // Not logged in
        if (! $role) {
            return redirect()->route('login')->withErrors(['auth' => 'Acceso restringido. Por favor, inicia sesión.']);
        }

        // Administrador bypasses all checks
        if (strtolower(trim($role)) === 'administrador') {
            return $next($request);
        }

        // Normalize allowed roles
        $allowed = array_map(function($r){ return strtolower(trim($r)); }, $roles ?: []);

        if (in_array(strtolower(trim($role)), $allowed)) {
            return $next($request);
        }

        abort(403, 'Acceso no autorizado');
    }
}
