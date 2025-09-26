<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Sheets;

class GoogleSheetsService
{
    private $client;
    private $service;
    private $spreadsheetId;
    private $range;
    
    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');
        $this->range = env('GOOGLE_SHEETS_RANGE', 'Hoja 1!A:C');
        $this->initializeClient();
    }
    
    /**
     * Inicializar cliente de Google Sheets
     */
    private function initializeClient()
    {
        try {
            $credentialsPath = storage_path('app/private/' . basename(env('GOOGLE_SHEETS_CREDENTIALS_PATH')));
            
            $this->client = new Google_Client();
            $this->client->setApplicationName('Royal Canin Ranking System');
            $this->client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);
            $this->client->setAuthConfig($credentialsPath);
            $this->client->setAccessType('offline');
            
            $this->service = new Google_Service_Sheets($this->client);
            
        } catch (Exception $e) {
            Log::error('Error inicializando Google Sheets: ' . $e->getMessage());
            $this->service = null;
        }
    }
    
    /**
     * Obtener datos del spreadsheet
     */
    public function obtenerDatos()
    {
        if (!$this->service) {
            Log::error('Google Sheets no está disponible, usando datos simulados');
            return $this->obtenerDatosSimulados();
        }
        
        try {
            Log::info("Obteniendo datos de Google Sheets: {$this->spreadsheetId}, rango: {$this->range}");
            
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $this->range);
            $values = $response->getValues();
            
            if (empty($values)) {
                Log::warning('No se encontraron datos en el spreadsheet');
                return [];
            }
            
            $datosProcesados = $this->procesarDatos($values);
            
            Log::info('Datos procesados exitosamente: ' . count($datosProcesados) . ' registros');
            
            return $datosProcesados;
            
        } catch (Exception $e) {
            Log::error('Error obteniendo datos de Google Sheets: ' . $e->getMessage());
            return $this->obtenerDatosSimulados();
        }
    }
    
    /**
     * Procesar datos del spreadsheet
     */
    private function procesarDatos($values)
    {
        $datos = [];
        
        // Saltar primera fila si parece ser header
        $filas = $values;
        if (!empty($filas[0]) && !is_numeric($filas[0][2] ?? '')) {
            array_shift($filas);
        }
        
        foreach ($filas as $index => $fila) {
            // Validar que tenga al menos 3 columnas y datos básicos
            if (count($fila) >= 3 && !empty(trim($fila[0] ?? '')) && !empty(trim($fila[1] ?? ''))) {
                $codigo = strtoupper(trim($fila[0]));
                $email = trim($fila[1]);
                $recomendaciones = (int) ($fila[2] ?? 0);
                
                if ($recomendaciones >= 0) {
                    $datos[] = [
                        'codigo' => $codigo,
                        'email' => $email,
                        'recomendaciones' => $recomendaciones
                    ];
                }
            }
        }
        
        return $datos;
    }
    
    /**
     * Datos simulados para testing/fallback
     */
    private function obtenerDatosSimulados()
    {
        return [
            ['codigo' => 'CLI001', 'email' => 'test1@example.com', 'recomendaciones' => 15],
            ['codigo' => 'CLI002', 'email' => 'test2@example.com', 'recomendaciones' => 12],
            ['codigo' => 'CLI003', 'email' => 'test3@example.com', 'recomendaciones' => 8],
        ];
    }
    
    /**
     * Probar conexión con Google Sheets
     */
    public function probarConexion()
    {
        if (!$this->service) {
            return ['success' => false, 'message' => 'Servicio no inicializado'];
        }
        
        try {
            $response = $this->service->spreadsheets->get($this->spreadsheetId);
            return [
                'success' => true, 
                'message' => 'Conexión exitosa',
                'titulo' => $response->getProperties()->getTitle()
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}