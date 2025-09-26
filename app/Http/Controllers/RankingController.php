<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranking;
use Illuminate\Support\Facades\Session;

class RankingController extends Controller
{
    /**
     * Mostrar página de ranking
     */
    public function index()
    {
        // Obtener el código de la clínica logueada
        $clinicCode = Session::get('clinic_code');
        
        // Obtener el ranking actual
        $rankings = Ranking::getRankingActual();
        
        // Buscar la posición de la clínica logueada
        $clinicaLogueada = $rankings->where('codigo', $clinicCode)->first();
        
        return view('pages.ranking', [
            'rankings' => $rankings,
            'clinicaLogueada' => $clinicaLogueada,
            'clinicCode' => $clinicCode
        ]);
    }
}
