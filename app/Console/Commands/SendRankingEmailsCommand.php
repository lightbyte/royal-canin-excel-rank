<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use App\Services\RankingService;
use Illuminate\Support\Facades\Log;

class SendRankingEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-ranking-update {--test=* : Enviar email de prueba a direcciones específicas} {--force : Forzar envío independientemente del día} {--limit= : Limitar el número de emails a enviar} {--offset= : Número de registros a saltar antes de empezar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar emails de actualización de ranking a todas las clínicas';

    protected $emailService;
    protected $rankingService;

    public function __construct(EmailService $emailService, RankingService $rankingService)
    {
        parent::__construct();
        $this->emailService = $emailService;
        $this->rankingService = $rankingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Verificar si es modo de prueba
        if ($this->option('test')) {
            return $this->manejarEmailsPrueba();
        }
        
        $this->info('📧 Iniciando envío de emails de ranking...');
        
        try {
            // Verificar si es el día correcto (a menos que se fuerce)
            if (!$this->option('force') && !$this->esDiaDeActualizacion()) {
                $diaConfiguracion = env('RANKING_UPDATE_DAY', 'wednesday');
                $this->warn("⚠️  Hoy no es día de envío de emails. El día configurado es: {$diaConfiguracion}");
                $this->info('💡 Usa --force para forzar el envío');
                return Command::FAILURE;
            }
            
            // Verificar configuración de email
            $this->info('🔍 Verificando configuración de email...');
            $configuracion = $this->emailService->verificarConfiguracion();
            
            if (!$configuracion['valida']) {
                $this->error('❌ Configuración de email inválida:');
                foreach ($configuracion['errores'] as $error) {
                    $this->error('  - ' . $error);
                }
                return Command::FAILURE;
            }
            
            if (env('MAIL_MAILER') === 'log') {
                $this->warn('⚠️  Los emails se guardarán en log, no se enviarán realmente');
            }
            
            // Mostrar estadísticas previas
            $this->mostrarEstadisticas();

            // Leer y normalizar parámetros de paginación
            $limitOpt = $this->option('limit');
            $offsetOpt = $this->option('offset');
            $limit = $limitOpt !== null ? (int) $limitOpt : null;
            // Si limit <= 0, lo tratamos como "sin límite"
            $limit = ($limit !== null && $limit <= 0) ? null : $limit;
            $offset = $offsetOpt !== null ? max(0, (int) $offsetOpt) : 0;

            if ($limit !== null || $offset > 0) {
                $this->info(sprintf('⚙️ Parámetros de envío: offset=%d%s', $offset, $limit !== null ? " limit={$limit}" : ' (sin límite)'));
            }

            // // Confirmar envío
            // if (!$this->option('force') && !$this->confirm('¿Continuar con el envío de emails?')) {
            //     $this->info('❌ Envío cancelado por el usuario');
            //     return Command::SUCCESS;
            // }
            
            // Realizar envío
            $this->info('📤 Enviando emails...');
            $resultado = $this->emailService->enviarEmailsActualizacion($limit, $offset);
            
            if ($resultado['success']) {
                $this->info('✅ Emails enviados exitosamente!');
                $this->info("📧 Emails enviados: {$resultado['emails_enviados']}");
                
                if ($resultado['errores'] > 0) {
                    $this->warn("⚠️  Errores en envío: {$resultado['errores']}");
                    
                    if (!empty($resultado['detalles_errores'])) {
                        $this->info('📋 Detalles de errores:');
                        foreach ($resultado['detalles_errores'] as $error) {
                            $this->error("  - {$error['clinica']}: {$error['error']}");
                        }
                    }
                }
                
                Log::info('Comando emails:send-ranking-update ejecutado exitosamente', [
                    'emails_enviados' => $resultado['emails_enviados'],
                    'errores' => $resultado['errores']
                ]);
                
                return Command::SUCCESS;
            } else {
                $this->error('❌ Error al enviar emails: ' . $resultado['message']);
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error inesperado: ' . $e->getMessage());
            Log::error('Error en comando emails:send-ranking-update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
    
    /**
     * Manejar envío de emails de prueba
     */
    private function manejarEmailsPrueba()
    {
        $emailsPrueba = $this->option('test');
        
        $this->info('🧪 Modo de prueba activado');
        $this->info('📧 Enviando emails de prueba a: ' . implode(', ', $emailsPrueba));
        
        $exitosos = 0;
        $errores = 0;
        
        foreach ($emailsPrueba as $email) {
            try {
                $resultado = $this->emailService->enviarEmailPrueba($email);
                
                if ($resultado['success']) {
                    $this->info("✅ Email de prueba enviado a: {$email}");
                    $exitosos++;
                } else {
                    $this->error("❌ Error enviando a {$email}: {$resultado['message']}");
                    $errores++;
                }
            } catch (\Exception $e) {
                $this->error("❌ Error enviando a {$email}: {$e->getMessage()}");
                $errores++;
            }
        }
        
        $this->info("📊 Resumen: {$exitosos} exitosos, {$errores} errores");
        
        return $errores > 0 ? Command::FAILURE : Command::SUCCESS;
    }
    
    /**
     * Mostrar estadísticas de emails
     */
    private function mostrarEstadisticas()
    {
        try {
            $estadisticas = $this->emailService->obtenerEstadisticas();
            
            $this->info('📊 Estadísticas de envío:');
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Total destinatarios', $estadisticas['total_destinatarios']],
                    ['Configuración válida', $estadisticas['configuracion_valida'] ? 'Sí' : 'No'],
                    ['Tipo de email', $estadisticas['tipo_email_actual']],
                    ['Próximo envío', $estadisticas['fecha_proximo_envio']]
                ]
            );
        } catch (\Exception $e) {
            $this->warn('⚠️  No se pudieron obtener estadísticas: ' . $e->getMessage());
        }
    }

    private function esDiaDeActualizacion()
    {
        $diaConfiguracion = env('RANKING_UPDATE_DAY', 'wednesday');
        $diaActual = strtolower(now()->format('l'));
        
        return $diaActual === $diaConfiguracion;
    }
}
