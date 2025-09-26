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
    public function enviarEmailsActualizacion()
    {
        try {
            Log::info('Iniciando envío de emails de actualización de ranking');
            
            // Obtener todas las clínicas del ranking actual
            $clinicas = Ranking::getRankingActual();
            
            if ($clinicas->isEmpty()) {
                throw new \Exception('No hay clínicas en el ranking actual');
            }
            
            // Determinar tipo de email según la fecha
            $tipoEmail = $this->determinarTipoEmail();
            
            $emailsEnviados = 0;
            $errores = [];
            
            foreach ($clinicas as $clinica) {
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
                'errores' => count($errores),
                'tipo_email' => $tipoEmail
            ]);
            
            return [
                'success' => true,
                'emails_enviados' => $emailsEnviados,
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
        $fechaInicio = Carbon::parse(env('RANKING_START_DATE', '2025-01-03'));
        $fechaFin = Carbon::parse(env('RANKING_END_DATE', '2025-12-12'));
        $fechaPrimerEmail = Carbon::parse(env('RANKING_FIRST_EMAIL_DATE', '2025-01-03'));
        
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
        
        // Por simplicidad, usamos log en lugar de envío real
        // En producción, aquí se usaría Mail::send()
        Log::info('Email enviado (simulado)', [
            'destinatario' => $clinica->email,
            'codigo' => $clinica->codigo,
            'tipo' => $tipoEmail,
            'asunto' => $datosEmail['asunto']
        ]);
        
        // Descomentar para envío real:
        /*
        Mail::send(
            'emails.ranking.' . $tipoEmail,
            $datosEmail,
            function ($message) use ($clinica, $datosEmail) {
                $message->to($clinica->email)
                        ->subject($datosEmail['asunto']);
            }
        );
        */
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
                    'mensaje_principal' => 'Te damos la bienvenida al sistema de ranking de clínicas Royal Canin.'
                ]);
                
            case 'final':
                return array_merge($datosBase, [
                    'asunto' => 'Ranking Final - Royal Canin',
                    'titulo' => 'Ranking Final del Período',
                    'mensaje_principal' => 'Ha finalizado el período de ranking. Aquí tienes tu posición final.',
                    'posicion_final' => $clinica->posicion_actual,
                    'puntos_totales' => $clinica->recomendaciones
                ]);
                
            default: // weekly
                return array_merge($datosBase, [
                    'asunto' => 'Actualización Semanal del Ranking - Royal Canin',
                    'titulo' => 'Actualización Semanal del Ranking',
                    'mensaje_principal' => 'Tu ranking ha sido actualizado. Consulta tu nueva posición.'
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
     * Verificar configuración de email
     */
    public function verificarConfiguracion()
    {
        $errores = [];
        
        if (empty(env('MAIL_FROM_ADDRESS'))) {
            $errores[] = 'MAIL_FROM_ADDRESS no está configurado';
        }
        
        if (empty(env('MAIL_FROM_NAME'))) {
            $errores[] = 'MAIL_FROM_NAME no está configurado';
        }
        
        if (env('MAIL_MAILER') === 'log') {
            $errores[] = 'MAIL_MAILER está configurado como "log" - los emails no se enviarán realmente';
        }
        
        return [
            'valida' => empty($errores),
            'errores' => $errores
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