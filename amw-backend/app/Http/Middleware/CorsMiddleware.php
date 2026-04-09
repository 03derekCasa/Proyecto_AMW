<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Permite el acceso desde todos los dominios
        // Puedes reemplazar "*" con un dominio específico para producción
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*') // Permite todos los orígenes
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS') // Métodos permitidos
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization, Origin, Accept');
    }
}
