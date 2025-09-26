<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckClinicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si existe la sesión de clínica
        if (!Session::has('clinic_code')) {
            // Si no está autenticado, redirigir al login
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder al ranking');
        }

        return $next($request);
    }
}
