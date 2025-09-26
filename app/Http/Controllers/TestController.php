<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetsService;

class TestController extends Controller
{
    protected $googleSheetsService;

    public function __construct(GoogleSheetsService $googleSheetsService)
    {
        $this->googleSheetsService = $googleSheetsService;
    }

    /**
     * Probar la conexión con Google Sheets desde la web
     */
    public function testGoogleSheets()
    {
        try {
            $resultado = $this->googleSheetsService->probarConexion();
            
            if ($resultado['success']) {
                $datos = $this->googleSheetsService->obtenerDatos();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Conexión exitosa con Google Sheets',
                    'spreadsheet_title' => $resultado['title'],
                    'spreadsheet_id' => $resultado['spreadsheet_id'],
                    'total_records' => count($datos),
                    'sample_data' => array_slice($datos, 0, 3),
                    'timestamp' => now()->toDateTimeString()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la conexión con Google Sheets',
                    'error' => $resultado['error'],
                    'suggestions' => [
                        'Verifica el SPREADSHEET_ID en el archivo .env',
                        'Asegúrate de que el archivo de credenciales existe',
                        'Confirma que la cuenta de servicio tiene acceso al spreadsheet'
                    ],
                    'timestamp' => now()->toDateTimeString()
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al probar Google Sheets',
                'error' => $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

    /**
     * Página de prueba con interfaz visual
     */
    public function testPage()
    {
        return view('test.google-sheets');
    }
}