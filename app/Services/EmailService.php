<?php

namespace App\Services;

use App\Models\Ranking;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmailService
{
    /**
     * Enviar emails de actualización de ranking a todas las clínicas
     */
    public function enviarEmailsActualizacion($limit = null, $offset = 0)
    {
        try {
            Log::info('Iniciando envío de emails de actualización de ranking', [
                'limit' => $limit,
                'offset' => $offset
            ]);
            
            // Obtener clínicas del ranking actual respetando limit/offset
            $clinicas = Ranking::getRankingActual($limit, $offset);
            
            if ($clinicas->isEmpty()) {
                throw new \Exception('No hay clínicas en el ranking actual con los parámetros especificados');
            }
            
            // Determinar tipo de email según la fecha
            $tipoEmail = $this->determinarTipoEmail();
            
            $emailsEnviados = 0;
            $emailsInvalidos = 0;
            $errores = [];
            
            foreach ($clinicas as $clinica) {
                // Verificar si el email es válido
                if (!filter_var($clinica->email, FILTER_VALIDATE_EMAIL)) {
                    $emailsInvalidos++;
                    Log::warning('Email inválido encontrado', [
                        'codigo' => $clinica->codigo,
                        'email' => $clinica->email
                    ]);
                    continue;
                }
                
                try {
                    $this->enviarEmailIndividual($clinica, $tipoEmail);
                    $emailsEnviados++;
                } catch (\Exception $e) {
                    $errores[] = [
                        'clinica' => $clinica->codigo,
                        'error' => $e->getMessage()
                    ];
                    Log::error('Error enviando email a clínica: ' . $clinica->codigo, [
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            Log::info('Envío de emails completado', [
                'emails_enviados' => $emailsEnviados,
                'emails_invalidos' => $emailsInvalidos,
                'errores' => count($errores),
                'tipo_email' => $tipoEmail,
                'limit' => $limit,
                'offset' => $offset
            ]);
            
            return [
                'success' => true,
                'emails_enviados' => $emailsEnviados,
                'emails_invalidos' => $emailsInvalidos,
                'errores' => count($errores),
                'detalles_errores' => $errores
            ];
            
        } catch (\Exception $e) {
            Log::error('Error general en envío de emails: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error en envío de emails: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Determinar el tipo de email según la fecha actual
     */
    private function determinarTipoEmail()
    {
        $fechaActual = now();
        $fechaInicio = Carbon::parse(env('RANKING_START_DATE', '2025-10-08'));
        $fechaFin = Carbon::parse(env('RANKING_END_DATE', '2025-12-03'));
        $fechaPrimerEmail = Carbon::parse(env('RANKING_FIRST_EMAIL_DATE', '2025-10-08'));
        
        // Email inicial (primer día)
        if ($fechaActual->isSameDay($fechaPrimerEmail)) {
            return 'first';
        }
        
        // Email final (último día)
        if ($fechaActual->isSameDay($fechaFin)) {
            return 'final';
        }
        
        // Email semanal estándar
        return 'weekly';
    }
    
    /**
     * Enviar email individual a una clínica
     */
    private function enviarEmailIndividual($clinica, $tipoEmail)
    {
        $datosEmail = $this->prepararDatosEmail($clinica, $tipoEmail);
        
        try {
            // Envío real de email
            Mail::send(
                'emails.ranking.' . $tipoEmail,
                $datosEmail,
                function ($message) use ($clinica, $datosEmail) {
                    $message->to($clinica->email)
                            ->subject($datosEmail['asunto'])
                            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                }
            );
            
            Log::info('Email enviado exitosamente', [
                'destinatario' => $clinica->email,
                'codigo' => $clinica->codigo,
                'tipo' => $tipoEmail,
                'asunto' => $datosEmail['asunto']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error enviando email', [
                'destinatario' => $clinica->email,
                'codigo' => $clinica->codigo,
                'tipo' => $tipoEmail,
                'error' => $e->getMessage()
            ]);
            
            throw $e; // Re-lanzar la excepción para manejo en nivel superior
        }
    }
    
    /**
     * Preparar datos para el email
     */
    private function prepararDatosEmail($clinica, $tipoEmail)
    {
        $datosBase = [
            'codigo' => $clinica->codigo,
            'posicion' => $clinica->posicion_actual,
            'puntos' => $clinica->recomendaciones,
            'variacion' => $clinica->variacion,
            'variacion_formateada' => $clinica->variacion_formateada,
            'fecha' => now()->format('d/m/Y'),
            'semana' => now()->format('Y-W')
        ];
        
        switch ($tipoEmail) {
            case 'first':
                return array_merge($datosBase, [
                    'asunto' => '¡Bienvenido al Ranking Royal Canin!',
                    'titulo' => 'Bienvenido al Sistema de Ranking',
                    'btn_link' => route('home'),
                ]);
                
            case 'final':
                return array_merge($datosBase, [
                    'asunto' => 'Ranking Final - Royal Canin',
                    'titulo' => 'Ranking Final del Período',
                    'btn_link' => route('home'),
                ]);
                
            default: // weekly
                return array_merge($datosBase, [
                    'asunto' => 'Actualización Semanal del Ranking - Royal Canin',
                    'titulo' => 'Actualización Semanal del Ranking',
                    'btn_link' => route('home'),
                ]);
        }
    }
    
    /**
     * Enviar email de prueba
     */
    public function enviarEmailPrueba($email, $codigo = 'CLI001')
    {
        try {
            // Buscar clínica o crear datos de prueba
            $clinica = Ranking::buscarPorCodigo($codigo);
            
            if (!$clinica) {
                // Crear datos de prueba
                $clinica = (object) [
                    'codigo' => $codigo,
                    'email' => $email,
                    'posicion_actual' => 1,
                    'recomendaciones' => 150,
                    'variacion' => 2,
                    'variacion_formateada' => '+2'
                ];
            }
            
            $this->enviarEmailIndividual($clinica, 'weekly');
            
            return [
                'success' => true,
                'message' => 'Email de prueba enviado exitosamente'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error enviando email de prueba: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error enviando email de prueba: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Enviar email de prueba con tipo específico
     */
    public function enviarEmailPruebaTipo($email, $codigo = 'CLI001', $tipo = 'weekly')
    {
        try {
            // Buscar clínica o crear datos de prueba
            $clinica = Ranking::buscarPorCodigo($codigo);
            
            if (!$clinica) {
                // Crear datos de prueba con diferentes posiciones según el tipo
                $datosSegunTipo = $this->obtenerDatosPruebaPorTipo($tipo);
                
                $clinica = (object) [
                    'codigo' => $codigo,
                    'email' => $email,
                    'posicion_actual' => $datosSegunTipo['posicion'],
                    'recomendaciones' => $datosSegunTipo['recomendaciones'],
                    'variacion' => $datosSegunTipo['variacion'],
                    'variacion_formateada' => $datosSegunTipo['variacion_formateada']
                ];
            }
            
            $this->enviarEmailIndividual($clinica, $tipo);
            
            return [
                'success' => true,
                'message' => "Email de prueba tipo '{$tipo}' enviado exitosamente"
            ];
            
        } catch (\Exception $e) {
            Log::error("Error enviando email de prueba tipo '{$tipo}': " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => "Error enviando email de prueba tipo '{$tipo}': " . $e->getMessage()
            ];
        }
    }

    /**
     * Enviar todas las plantillas de email de prueba
     */
    public function enviarTodasLasPlantillasPrueba($email, $codigo = 'CLI001')
    {
        $tipos = ['first', 'weekly', 'final'];
        $resultados = [];
        $exitosos = 0;
        $errores = 0;
        
        foreach ($tipos as $tipo) {
            try {
                $resultado = $this->enviarEmailPruebaTipo($email, $codigo, $tipo);
                $resultados[] = [
                    'tipo' => $tipo,
                    'success' => $resultado['success'],
                    'message' => $resultado['message']
                ];
                
                if ($resultado['success']) {
                    $exitosos++;
                } else {
                    $errores++;
                }
                
                // Pequeña pausa entre envíos para evitar problemas con el servidor SMTP
                sleep(1);
                
            } catch (\Exception $e) {
                $resultados[] = [
                    'tipo' => $tipo,
                    'success' => false,
                    'message' => "Error: " . $e->getMessage()
                ];
                $errores++;
            }
        }
        
        return [
            'success' => $errores === 0,
            'exitosos' => $exitosos,
            'errores' => $errores,
            'total' => count($tipos),
            'detalles' => $resultados,
            'message' => "Enviados {$exitosos} de " . count($tipos) . " emails de prueba"
        ];
    }

    /**
     * Obtener datos de prueba específicos por tipo de email
     */
    private function obtenerDatosPruebaPorTipo($tipo)
    {
        switch ($tipo) {
            case 'first':
                return [
                    'posicion' => 5,
                    'recomendaciones' => 120,
                    'variacion' => 0,
                    'variacion_formateada' => 'Nuevo'
                ];
            
            case 'weekly':
                return [
                    'posicion' => 3,
                    'recomendaciones' => 180,
                    'variacion' => 2,
                    'variacion_formateada' => '+2'
                ];
            
            case 'final':
                return [
                    'posicion' => 1,
                    'recomendaciones' => 250,
                    'variacion' => 2,
                    'variacion_formateada' => '+2'
                ];
            
            default:
                return [
                    'posicion' => 1,
                    'recomendaciones' => 150,
                    'variacion' => 0,
                    'variacion_formateada' => '='
                ];
        }
    }
    
    /**
     * Verificar configuración de email
     */
    public function verificarConfiguracion()
    {
        $errores = [];
        $advertencias = [];
        
        if (empty(env('MAIL_FROM_ADDRESS'))) {
            $errores[] = 'MAIL_FROM_ADDRESS no está configurado';
        }
        
        if (empty(env('MAIL_FROM_NAME'))) {
            $errores[] = 'MAIL_FROM_NAME no está configurado';
        }
        
        if (empty(env('MAIL_HOST'))) {
            $errores[] = 'MAIL_HOST no está configurado';
        }
        
        if (empty(env('MAIL_USERNAME'))) {
            $errores[] = 'MAIL_USERNAME no está configurado';
        }
        
        if (empty(env('MAIL_PASSWORD'))) {
            $errores[] = 'MAIL_PASSWORD no está configurado';
        }
        
        if (env('MAIL_MAILER') === 'log') {
            $advertencias[] = 'MAIL_MAILER está configurado como "log" - los emails no se enviarán realmente';
        }
        
        if (env('MAIL_PORT') == 465 && empty(env('MAIL_ENCRYPTION'))) {
            $errores[] = 'Puerto 465 requiere MAIL_ENCRYPTION=ssl';
        }
        
        if (env('MAIL_PORT') == 587 && env('MAIL_ENCRYPTION') !== 'tls') {
            $advertencias[] = 'Puerto 587 generalmente requiere MAIL_ENCRYPTION=tls';
        }
        
        return [
            'valida' => empty($errores),
            'errores' => $errores,
            'advertencias' => $advertencias ?? []
        ];
    }
    
    /**
     * Obtener estadísticas de emails
     */
    public function obtenerEstadisticas()
    {
        $totalClinicas = Ranking::getRankingActual()->count();
        
        return [
            'total_destinatarios' => $totalClinicas,
            'configuracion_valida' => $this->verificarConfiguracion()['valida'],
            'tipo_email_actual' => $this->determinarTipoEmail(),
            'fecha_proximo_envio' => $this->calcularProximoEnvio()
        ];
    }
    
    /**
     * Calcular fecha del próximo envío
     */
    private function calcularProximoEnvio()
    {
        $proximoMiercoles = now()->next(Carbon::WEDNESDAY);
        $horaEnvio = env('RANKING_UPDATE_HOUR', '08:00');
        
        return $proximoMiercoles->format('Y-m-d') . ' ' . $horaEnvio;
    }
}