<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ranking;
use Carbon\Carbon;

class ListRankingsCommand extends Command
{
    protected $signature = 'ranking:list {--all : Mostrar todos los registros incluyendo inactivos} {--week= : Mostrar ranking de una semana específica (formato: 2025-W01)} {--limit=10 : Número de registros a mostrar (0 = todos)}';
    protected $description = 'Mostrar los registros de la tabla ranking';

    public function handle()
    {
        $mostrarTodos = $this->option('all');
        $semana = $this->option('week');
        $limite = (int) $this->option('limit');
        
        $this->info('📊 Registros de la tabla ranking');
        $this->newLine();
        
        // Construir query base para contar total
        $queryBase = Ranking::query();
        
        if (!$mostrarTodos) {
            $queryBase->where('activo', true);
        }
        
        if ($semana) {
            $queryBase->where('semana', $semana);
            $this->info("🗓️  Mostrando semana: {$semana}");
        } else {
            $semanaActual = now()->format('Y-W');
            $queryBase->where('semana', $semanaActual);
            $this->info("🗓️  Mostrando semana actual: {$semanaActual}");
        }
        
        // Contar total de registros disponibles
        $totalDisponibles = $queryBase->count();
        
        if ($totalDisponibles === 0) {
            $this->warn('⚠️  No se encontraron registros con los criterios especificados');
            return Command::SUCCESS;
        }
        
        // Construir query para mostrar
        $query = clone $queryBase;
        $query->orderBy('posicion_actual');
        
        if ($limite > 0 && $limite < $totalDisponibles) {
            $query->limit($limite);
            $this->info("📋 Mostrando {$limite} de {$totalDisponibles} registros disponibles");
            $this->info("💡 Usa --limit=0 para ver todos los registros");
        } else {
            $this->info("📋 Mostrando todos los {$totalDisponibles} registros disponibles");
        }
        
        $rankings = $query->get();
        
        // Mostrar estadísticas
        $this->mostrarEstadisticas($rankings, $totalDisponibles);
        $this->newLine();
        
        // Mostrar tabla
        $this->mostrarTabla($rankings);
        
        return Command::SUCCESS;
    }
    
    private function mostrarEstadisticas($rankings, $totalDisponibles)
    {
        $total = $rankings->count();
        $activos = $rankings->where('activo', true)->count();
        $inactivos = $rankings->where('activo', false)->count();
        $conVariacion = $rankings->whereNotNull('variacion')->count();
        
        $this->table(
            ['Estadística', 'Valor'],
            [
                ['Registros mostrados', $total],
                ['Total disponibles', $totalDisponibles],
                ['Activos', $activos],
                ['Inactivos', $inactivos],
                ['Con variación', $conVariacion],
                ['Promedio recomendaciones', round($rankings->avg('recomendaciones'), 2)],
                ['Máximo recomendaciones', $rankings->max('recomendaciones')],
                ['Mínimo recomendaciones', $rankings->min('recomendaciones')]
            ]
        );
    }
    
    private function mostrarTabla($rankings)
    {
        $headers = ['Pos.', 'Código', 'Email', 'Recomend.', 'Variación', 'Estado', 'Semana'];
        $rows = [];
        
        foreach ($rankings as $ranking) {
            $variacion = $this->formatearVariacion($ranking->variacion);
            $estado = $ranking->activo ? '✅ Activo' : '❌ Inactivo';
            
            $rows[] = [
                $ranking->posicion_actual,
                $ranking->codigo,
                $ranking->email,
                $ranking->recomendaciones,
                $variacion,
                $estado,
                $ranking->semana
            ];
        }
        
        $this->table($headers, $rows);
    }
    
    private function formatearVariacion($variacion)
    {
        if ($variacion === null) {
            return '➖ Nuevo';
        }
        
        if ($variacion > 0) {
            return "⬆️ +{$variacion}";
        } elseif ($variacion < 0) {
            return "⬇️ {$variacion}";
        } else {
            return "➡️ =";
        }
    }
}