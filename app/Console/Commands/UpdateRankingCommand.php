<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RankingService;
use App\Services\GoogleSheetsService;

class UpdateRankingCommand extends Command
{
    protected $signature = 'ranking:update {--force : Forzar actualización independientemente del día}';
    protected $description = 'Actualizar el ranking de clínicas desde Google Sheets';

    private $rankingService;
    private $googleSheetsService;

    public function __construct(RankingService $rankingService, GoogleSheetsService $googleSheetsService)
    {
        parent::__construct();
        $this->rankingService = $rankingService;
        $this->googleSheetsService = $googleSheetsService;
    }

    public function handle()
    {
        $this->info('🚀 Iniciando actualización del ranking...');
        
        try {
            // Verificar día de actualización (a menos que se fuerce)
            if (!$this->option('force') && !$this->esDiaDeActualizacion()) {
                $this->warn('⚠️  Hoy no es día de actualización. Usa --force para forzar.');
                return Command::FAILURE;
            }
            
            // Probar conexión con Google Sheets
            $this->info('🔗 Verificando conexión con Google Sheets...');
            $conexion = $this->googleSheetsService->probarConexion();
            
            if (!$conexion['success']) {
                $this->error('❌ Error de conexión: ' . $conexion['message']);
                return Command::FAILURE;
            }
            
            $this->info('✅ Conexión exitosa: ' . $conexion['titulo']);
            
            // Confirmar actualización
            if (!$this->option('force') && !$this->confirm('¿Continuar con la actualización del ranking?')) {
                $this->info('❌ Actualización cancelada');
                return Command::SUCCESS;
            }
            
            // Actualizar ranking
            $this->info('📊 Actualizando ranking...');
            $resultado = $this->rankingService->actualizarRanking();
            
            $this->info('✅ Ranking actualizado exitosamente');
            $this->table(['Métrica', 'Valor'], [
                ['Clínicas procesadas', $resultado['total_clinicas']],
                ['Registros antiguos eliminados', $resultado['eliminados']],
                ['Semana', now()->format('Y-W')]
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    private function esDiaDeActualizacion()
    {
        $diaConfiguracion = env('RANKING_UPDATE_DAY', 'wednesday');
        $diaActual = strtolower(now()->format('l'));
        
        return $diaActual === $diaConfiguracion;
    }
}
