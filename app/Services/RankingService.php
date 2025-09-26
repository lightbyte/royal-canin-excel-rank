<?php

namespace App\Services;

use App\Models\Ranking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RankingService
{
    protected $googleSheetsService;
    
    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }
    
    /**
     * Actualizar el ranking completo desde Google Sheets
     */
    public function actualizarRanking()
    {
        try {
            Log::info('Iniciando actualización de ranking');
            
            // Obtener datos de Google Sheets
            $datosGoogleSheets = $this->googleSheetsService->obtenerDatos();
            
            if (empty($datosGoogleSheets)) {
                throw new \Exception('No se obtuvieron datos de Google Sheets');
            }
            
            // Procesar datos
            $datosProcesados = $this->googleSheetsService->procesarDatos($datosGoogleSheets);
            
            // Ordenar por recomendaciones (descendente)
            usort($datosProcesados, function($a, $b) {
                return $b['recomendaciones'] <=> $a['recomendaciones'];
            });
            
            // Obtener ranking anterior para calcular variaciones
            $rankingAnterior = $this->obtenerRankingAnterior();
            
            // Generar nuevo ranking
            $nuevoRanking = $this->generarNuevoRanking($datosProcesados, $rankingAnterior);
            
            // Guardar en base de datos
            $this->guardarRanking($nuevoRanking);
            
            Log::info('Ranking actualizado exitosamente', [
                'total_clinicas' => count($nuevoRanking),
                'semana' => now()->format('Y-W')
            ]);
            
            return [
                'success' => true,
                'message' => 'Ranking actualizado exitosamente',
                'total_clinicas' => count($nuevoRanking)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar ranking: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al actualizar ranking: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener el ranking de la semana anterior
     */
    private function obtenerRankingAnterior()
    {
        $semanaAnterior = now()->subWeek()->format('Y-W');
        
        return Ranking::where('semana', $semanaAnterior)
            ->where('activo', true)
            ->get()
            ->keyBy('codigo');
    }
    
    /**
     * Generar nuevo ranking con variaciones calculadas
     */
    private function generarNuevoRanking($datosProcesados, $rankingAnterior)
    {
        $semanaActual = now()->format('Y-W');
        $nuevoRanking = [];
        
        foreach ($datosProcesados as $index => $clinica) {
            $posicionActual = $index + 1;
            $posicionAnterior = null;
            $variacion = null;
            
            // Buscar posición anterior
            if ($rankingAnterior->has($clinica['codigo'])) {
                $clinicaAnterior = $rankingAnterior->get($clinica['codigo']);
                $posicionAnterior = $clinicaAnterior->posicion_actual;
                $variacion = $posicionAnterior - $posicionActual;
            }
            
            $nuevoRanking[] = [
                'codigo' => $clinica['codigo'],
                'email' => $clinica['email'],
                'recomendaciones' => $clinica['recomendaciones'],
                'posicion_actual' => $posicionActual,
                'posicion_anterior' => $posicionAnterior,
                'variacion' => $variacion,
                'semana' => $semanaActual,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        return $nuevoRanking;
    }
    
    /**
     * Guardar el nuevo ranking en la base de datos
     */
    private function guardarRanking($nuevoRanking)
    {
        DB::transaction(function() use ($nuevoRanking) {
            $semanaActual = now()->format('Y-W');
            
            // Marcar como inactivos los registros de la semana actual
            Ranking::where('semana', $semanaActual)
                ->update(['activo' => false]);
            
            // Insertar nuevo ranking
            Ranking::insert($nuevoRanking);
        });
    }
    
    /**
     * Obtener estadísticas del ranking actual
     */
    public function obtenerEstadisticas()
    {
        $semanaActual = now()->format('Y-W');
        
        $total = Ranking::semanaActual()->activos()->count();
        $nuevasClinicas = Ranking::semanaActual()->activos()->whereNull('posicion_anterior')->count();
        $conVariacionPositiva = Ranking::semanaActual()->activos()->where('variacion', '>', 0)->count();
        $conVariacionNegativa = Ranking::semanaActual()->activos()->where('variacion', '<', 0)->count();
        
        return [
            'total_clinicas' => $total,
            'nuevas_clinicas' => $nuevasClinicas,
            'mejoraron_posicion' => $conVariacionPositiva,
            'empeoraron_posicion' => $conVariacionNegativa,
            'semana' => $semanaActual
        ];
    }
    
    /**
     * Verificar si es día de actualización
     */
    public function esDiaDeActualizacion()
    {
        $diaConfiguracion = env('RANKING_UPDATE_DAY', 'wednesday');
        $diaActual = strtolower(now()->format('l'));
        
        return $diaActual === $diaConfiguracion;
    }
    
    /**
     * Verificar si es hora de actualización
     */
    public function esHoraDeActualizacion()
    {
        $horaConfiguracion = env('RANKING_UPDATE_HOUR', '07:00');
        $horaActual = now()->format('H:i');
        
        return $horaActual >= $horaConfiguracion;
    }
    
    /**
     * Limpiar rankings antiguos (mantener solo las últimas 4 semanas)
     */
    public function limpiarRankingsAntiguos()
    {
        $fechaLimite = now()->subWeeks(4)->format('Y-W');
        
        $eliminados = Ranking::where('semana', '<', $fechaLimite)->delete();
        
        Log::info('Rankings antiguos eliminados', ['eliminados' => $eliminados]);
        
        return $eliminados;
    }
}