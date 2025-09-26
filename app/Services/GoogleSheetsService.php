<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Sheets;

class GoogleSheetsService
{
    protected $spreadsheetId;
    protected $range;
    protected $credentialsPath;
    protected $client;
    protected $service;
    
    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');
        $this->range = env('GOOGLE_SHEETS_RANGE', 'Sheet1!A:C');
        $this->credentialsPath = storage_path('app/private/' . basename(env('GOOGLE_SHEETS_CREDENTIALS_PATH')));
        
        $this->initializeGoogleClient();
    }
    
    /**
     * Inicializar el cliente de Google
     */
    private function initializeGoogleClient()
    {
        try {
            $this->client = new Google_Client();
            $this->client->setApplicationName('Royal Canin Ranking System');
            $this->client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);
            $this->client->setAuthConfig($this->credentialsPath);
            $this->client->setAccessType('offline');
            
            $this->service = new Google_Service_Sheets($this->client);
            
        } catch (Exception $e) {
            Log::error('Error inicializando cliente de Google: ' . $e->getMessage());
            // No lanzamos excepción aquí para permitir fallback a datos simulados
        }
    }
    
    /**
     * Obtener datos del Google Spreadsheet
     * 
     * @return array
     * @throws Exception
     */
    public function obtenerDatos()
    {
        try {
            // Validar configuración antes de hacer la llamada
            $this->validarConfiguracion();
            
            Log::info('Obteniendo datos de Google Sheets - Spreadsheet ID: ' . $this->spreadsheetId);
            
            // Hacer la llamada real a Google Sheets API
            if ($this->service) {
                $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $this->range);
                $values = $response->getValues();
                
                if (empty($values)) {
                    Log::warning('No se encontraron datos en el spreadsheet');
                    return [];
                }
                
                // Procesar los datos obtenidos
                $datosProcesados = $this->procesarDatosRaw($values);
                
                Log::info('Datos obtenidos exitosamente de Google Sheets. Total de registros: ' . count($datosProcesados));
                
                return $datosProcesados;
            } else {
                throw new Exception('Cliente de Google Sheets no inicializado');
            }
            
        } catch (Exception $e) {
            Log::error('Error al obtener datos de Google Sheets: ' . $e->getMessage());
            
            // En caso de error, usar datos simulados como fallback
            Log::info('Usando datos simulados como fallback');
            return $this->obtenerDatosSimulados();
        }
    }
    
    /**
     * Procesar datos raw del spreadsheet
     */
    private function procesarDatosRaw($values)
    {
        $datosProcesados = [];
        
        // Saltar la primera fila si contiene headers
        $filas = $values;
        if (!empty($filas[0]) && !is_numeric($filas[0][2] ?? '')) {
            array_shift($filas); // Remover header
        }
        
        foreach ($filas as $fila) {
            // Validar que la fila tenga los datos necesarios
            if (count($fila) >= 3 && !empty($fila[0]) && !empty($fila[1])) {
                $codigo = strtoupper(trim($fila[0]));
                $email = strtolower(trim($fila[1]));
                $recomendaciones = (int) ($fila[2] ?? 0);
                
                // Validar formato de email básico
                if (filter_var($email, FILTER_VALIDATE_EMAIL) && $recomendaciones >= 0) {
                    $datosProcesados[] = [
                        'codigo' => $codigo,
                        'email' => $email,
                        'recomendaciones' => $recomendaciones
                    ];
                }
            }
        }
        
        return $datosProcesados;
    }
    
    /**
     * Datos simulados para desarrollo y fallback
     */
    private function obtenerDatosSimulados()
    {
        return [
            ['codigo' => 'CLI001', 'email' => 'clinica001@test.com', 'recomendaciones' => 155],
            ['codigo' => 'CLI002', 'email' => 'clinica002@test.com', 'recomendaciones' => 148],
            ['codigo' => 'CLI003', 'email' => 'clinica003@test.com', 'recomendaciones' => 142],
            ['codigo' => 'CLI004', 'email' => 'clinica004@test.com', 'recomendaciones' => 138],
            ['codigo' => 'CLI005', 'email' => 'clinica005@test.com', 'recomendaciones' => 133],
            ['codigo' => 'CLI006', 'email' => 'clinica006@test.com', 'recomendaciones' => 128],
            ['codigo' => 'CLI007', 'email' => 'clinica007@test.com', 'recomendaciones' => 122],
            ['codigo' => 'CLI008', 'email' => 'clinica008@test.com', 'recomendaciones' => 118],
            ['codigo' => 'CLI009', 'email' => 'clinica009@test.com', 'recomendaciones' => 112],
            ['codigo' => 'CLI010', 'email' => 'clinica010@test.com', 'recomendaciones' => 108],
            ['codigo' => 'CLI011', 'email' => 'clinica011@test.com', 'recomendaciones' => 95], // Nueva clínica
        ];
    }
    
    /**
     * Validar configuración de Google Sheets
     */
    public function validarConfiguracion()
    {
        if (empty($this->spreadsheetId)) {
            throw new Exception('GOOGLE_SHEETS_SPREADSHEET_ID no está configurado correctamente');
        }
        
        if (empty($this->credentialsPath) || !file_exists($this->credentialsPath)) {
            throw new Exception('Archivo de credenciales de Google no encontrado en: ' . $this->credentialsPath);
        }
        
        return true;
    }
    
    /**
     * Procesar datos del spreadsheet y convertirlos al formato esperado
     */
    public function procesarDatos($datosRaw)
    {
        $datosProcesados = [];
        
        foreach ($datosRaw as $fila) {
            // Validar que la fila tenga los datos necesarios
            if (count($fila) >= 3 && !empty($fila[0]) && !empty($fila[1])) {
                $datosProcesados[] = [
                    'codigo' => strtoupper(trim($fila[0])),
                    'email' => strtolower(trim($fila[1])),
                    'recomendaciones' => (int) ($fila[2] ?? 0)
                ];
            }
        }
        
        return $datosProcesados;
    }
    
    /**
     * Probar la conexión con Google Sheets
     */
    public function probarConexion()
    {
        try {
            $this->validarConfiguracion();
            
            if (!$this->service) {
                throw new Exception('Servicio de Google Sheets no inicializado');
            }
            
            // Intentar obtener información básica del spreadsheet
            $response = $this->service->spreadsheets->get($this->spreadsheetId);
            $title = $response->getProperties()->getTitle();
            
            Log::info('Conexión exitosa con Google Sheets. Título del documento: ' . $title);
            
            return [
                'success' => true,
                'title' => $title,
                'spreadsheet_id' => $this->spreadsheetId
            ];
            
        } catch (Exception $e) {
            Log::error('Error al probar conexión con Google Sheets: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}