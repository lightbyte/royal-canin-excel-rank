<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranking;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        // Si ya está autenticado, redirigir al ranking
        if (Session::has('clinic_code')) {
            return redirect()->route('ranking');
        }
        
        return view('pages.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50'
        ], [
            'codigo.required' => 'El código de clínica es obligatorio'
        ]);

        $codigo = strtoupper(trim($request->codigo));
        
        // Buscar el código en la base de datos
        $clinica = Ranking::buscarPorCodigo($codigo);
        
        if (!$clinica) {
            return back()->withErrors([
                'codigo' => 'Código de clínica no encontrado'
            ])->withInput();
        }

        // Crear sesión
        Session::put('clinic_code', $codigo);
        Session::put('clinic_email', $clinica->email);
        
        return redirect()->route('ranking');
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        Session::forget(['clinic_code', 'clinic_email']);
        return redirect()->route('home');
    }
}
