<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsService;

class TestGoogleSheetsCommand extends Command
{
    protected $signature = 'sheets:test';
    protected $description = 'Probar la conexión con Google Sheets';

    protected $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        parent::__construct();
        $this->googleSheetsService = $googleSheetsService;
    }

    public function handle()
    {
        $this->info('🔍 Probando conexión con Google Sheets...');
        $this->newLine();

        try {
            // Probar la conexión
            $resultado = $this->googleSheetsService->probarConexion();

            if ($resultado['success']) {
                $this->info('✅ Conexión exitosa con Google Sheets');
                $this->info('📄 Título del documento: ' . $resultado['title']);
                $this->info('🆔 Spreadsheet ID: ' . $resultado['spreadsheet_id']);
                $this->newLine();

                // Intentar obtener datos
                $this->info('📊 Obteniendo datos del spreadsheet...');
                $datos = $this->googleSheetsService->obtenerDatos();

                if (!empty($datos)) {
                    $this->info('✅ Datos obtenidos exitosamente');
                    $this->info('📈 Total de registros: ' . count($datos));
                    $this->newLine();

                    // Mostrar los primeros 5 registros
                    $this->info('📋 Primeros registros:');
                    $headers = ['Código', 'Email', 'Recomendaciones'];
                    $rows = [];

                    foreach (array_slice($datos, 0, 5) as $registro) {
                        $rows[] = [
                            $registro['codigo'],
                            $registro['email'],
                            $registro['recomendaciones']
                        ];
                    }

                    $this->table($headers, $rows);

                    if (count($datos) > 5) {
                        $this->info('... y ' . (count($datos) - 5) . ' registros más');
                    }
                } else {
                    $this->warn('⚠️  No se encontraron datos en el spreadsheet');
                }

            } else {
                $this->error('❌ Error en la conexión: ' . $resultado['error']);
                $this->newLine();
                $this->info('💡 Verifica:');
                $this->info('   - El SPREADSHEET_ID en el archivo .env');
                $this->info('   - Que el archivo de credenciales existe');
                $this->info('   - Que la cuenta de servicio tiene acceso al spreadsheet');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error inesperado: ' . $e->getMessage());
            $this->newLine();
            $this->info('💡 Revisa los logs para más detalles');
        }

        return 0;
    }
}