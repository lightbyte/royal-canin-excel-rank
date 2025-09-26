<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsService;

class TestGoogleSheetsCommand extends Command
{
    protected $signature = 'sheets:test {--data : Mostrar datos obtenidos}';
    protected $description = 'Probar conexión y datos de Google Sheets';

    private $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        parent::__construct();
        $this->googleSheetsService = $googleSheetsService;
    }

    public function handle()
    {
        $this->info('🔍 Probando Google Sheets...');
        
        // Probar conexión
        $this->info('📡 Probando conexión...');
        $conexion = $this->googleSheetsService->probarConexion();
        
        if ($conexion['success']) {
            $this->info('✅ Conexión exitosa');
            $this->info('📄 Spreadsheet: ' . $conexion['titulo']);
        } else {
            $this->error('❌ Error de conexión: ' . $conexion['message']);
            return Command::FAILURE;
        }
        
        // Obtener datos si se solicita
        if ($this->option('data')) {
            $this->info('📊 Obteniendo datos...');
            $datos = $this->googleSheetsService->obtenerDatos();
            
            $this->info('📈 Total de registros: ' . count($datos));
            
            if (!empty($datos)) {
                $this->table(['Código', 'Email', 'Recomendaciones'], 
                    array_slice($datos, 0, 10) // Solo primeros 10
                );
                
                if (count($datos) > 10) {
                    $this->info('... y ' . (count($datos) - 10) . ' registros más');
                }
            }
        }
        
        return Command::SUCCESS;
    }
}