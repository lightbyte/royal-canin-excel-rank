<?php

namespace App\Services;

use App\Models\Ranking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RankingService
{
    private $googleSheetsService;
    
    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }
    
    /**
     * Actualizar ranking desde Google Sheets
     */
    public function actualizarRanking()
    {
        try {
            Log::info('Iniciando actualización de ranking');
            
            // Obtener datos de Google Sheets (ya procesados)
            $datos = $this->googleSheetsService->obtenerDatos();
            
            if (empty($datos)) {
                throw new \Exception('No se obtuvieron datos de Google Sheets');
            }
            
            // Ordenar por recomendaciones descendente
            usort($datos, fn($a, $b) => $b['recomendaciones'] <=> $a['recomendaciones']);
            
            // Obtener ranking anterior para calcular variaciones
            $rankingAnterior = $this->obtenerRankingAnterior();
            
            // Crear nuevo ranking con posiciones y variaciones
            $nuevoRanking = $this->crearRanking($datos, $rankingAnterior);
            
            // Guardar en base de datos
            $totalGuardados = $this->guardarRanking($nuevoRanking);
            
            
            Log::info('Ranking actualizado exitosamente', [
                'total_clinicas' => $totalGuardados,
                'semana' => now()->format('Y-W'),
                'eliminados' => $eliminados
            ]);
            
            return [
                'success' => true,
                'total_clinicas' => $totalGuardados,
                'eliminados' => $eliminados
            ];
            
        } catch (\Exception $e) {
            Log::error('Error actualizando ranking: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener ranking de la semana anterior
     */
    private function obtenerRankingAnterior()
    {
        return Ranking::all()
            ->pluck('posicion_actual', 'codigo')
            ->toArray();
    }
    
    /**
     * Crear ranking con posiciones y variaciones
     */
    private function crearRanking($datos, $rankingAnterior)
    {
        $ranking = [];
        $semanaActual = now()->format('Y-W');
        
        foreach ($datos as $index => $clinica) {
            $posicionActual = $index + 1;
            $posicionAnterior = $rankingAnterior[$clinica['codigo']] ?? null;
            
            // Calcular variación
            $variacion = 0;
            if ($posicionAnterior !== null) {
                $variacion = $posicionAnterior - $posicionActual;
            }
            
            $ranking[] = [
                'codigo' => $clinica['codigo'],
                'email' => $clinica['email'],
                'recomendaciones' => $clinica['recomendaciones'],
                'posicion_actual' => $posicionActual,
                'posicion_anterior' => $posicionAnterior,
                'variacion' => $variacion,
                'semana' => $semanaActual,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        return $ranking;
    }
    
    /**
     * Guardar ranking en base de datos
     */
    private function guardarRanking($ranking)
    {
        DB::beginTransaction();
        
        try {
            // Eliminar ranking actual si existe
            Ranking::all()->delete();
            
            // Insertar nuevo ranking
            Ranking::insert($ranking);
            
            DB::commit();
            
            return count($ranking);
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
}