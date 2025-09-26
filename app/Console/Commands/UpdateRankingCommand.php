<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RankingService;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\Log;

class UpdateRankingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ranking:update {--force : Forzar actualización independientemente del día}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar el ranking de clínicas desde Google Sheets';

    protected $rankingService;
    protected $googleSheetsService;

    public function __construct(RankingService $rankingService, GoogleSheetsService $googleSheetsService)
    {
        parent::__construct();
        $this->rankingService = $rankingService;
        $this->googleSheetsService = $googleSheetsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando actualización del ranking...');
        
        try {
            // Verificar si es el día correcto (a menos que se fuerce)
            if (!$this->option('force') && !$this->rankingService->esDiaDeActualizacion()) {
                $diaConfiguracion = env('RANKING_UPDATE_DAY', 'wednesday');
                $this->warn("⚠️  Hoy no es día de actualización. El día configurado es: {$diaConfiguracion}");
                $this->info('💡 Usa --force para forzar la actualización');
                return Command::FAILURE;
            }
            
            // Verificar configuración de Google Sheets
            $this->info('🔍 Verificando configuración de Google Sheets...');
            try {
                $this->googleSheetsService->validarConfiguracion();
                $this->info('✅ Configuración de Google Sheets válida');
            } catch (\Exception $e) {
                $this->error('❌ Error en configuración de Google Sheets: ' . $e->getMessage());
                return Command::FAILURE;
            }
            
            // Mostrar información previa
            $this->mostrarInformacionPrevia();
            
            // Confirmar actualización
            if (!$this->option('force') && !$this->confirm('¿Continuar con la actualización del ranking?')) {
                $this->info('❌ Actualización cancelada por el usuario');
                return Command::SUCCESS;
            }
            
            // Realizar actualización
            $this->info('📊 Obteniendo datos de Google Sheets...');
            $resultado = $this->rankingService->actualizarRanking();
            
            if ($resultado['success']) {
                $this->info('✅ Ranking actualizado exitosamente!');
                $this->info("📈 Total de clínicas procesadas: {$resultado['total_clinicas']}");
                
                // Mostrar estadísticas
                $this->mostrarEstadisticas();
                
                // Limpiar rankings antiguos
                $this->info('🧹 Limpiando rankings antiguos...');
                $eliminados = $this->rankingService->limpiarRankingsAntiguos();
                $this->info("🗑️  Registros antiguos eliminados: {$eliminados}");
                
                Log::info('Comando ranking:update ejecutado exitosamente', [
                    'total_clinicas' => $resultado['total_clinicas'],
                    'registros_eliminados' => $eliminados
                ]);
                
                return Command::SUCCESS;
            } else {
                $this->error('❌ Error al actualizar ranking: ' . $resultado['message']);
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error inesperado: ' . $e->getMessage());
            Log::error('Error en comando ranking:update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
    
    /**
     * Mostrar información previa a la actualización
     */
    private function mostrarInformacionPrevia()
    {
        $this->info('📋 Información actual:');
        
        try {
            $estadisticas = $this->rankingService->obtenerEstadisticas();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Total clínicas actuales', $estadisticas['total_clinicas']],
                    ['Nuevas clínicas', $estadisticas['nuevas_clinicas']],
                    ['Mejoraron posición', $estadisticas['mejoraron_posicion']],
                    ['Empeoraron posición', $estadisticas['empeoraron_posicion']],
                    ['Semana actual', $estadisticas['semana']]
                ]
            );
        } catch (\Exception $e) {
            $this->warn('⚠️  No se pudieron obtener estadísticas previas: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar estadísticas después de la actualización
     */
    private function mostrarEstadisticas()
    {
        try {
            $estadisticas = $this->rankingService->obtenerEstadisticas();
            
            $this->info('📊 Estadísticas actualizadas:');
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Total clínicas', $estadisticas['total_clinicas']],
                    ['Nuevas clínicas', $estadisticas['nuevas_clinicas']],
                    ['Mejoraron posición', $estadisticas['mejoraron_posicion']],
                    ['Empeoraron posición', $estadisticas['empeoraron_posicion']],
                    ['Semana', $estadisticas['semana']]
                ]
            );
        } catch (\Exception $e) {
            $this->warn('⚠️  No se pudieron obtener estadísticas: ' . $e->getMessage());
        }
    }
}
