<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email} {--code=CLI001 : Código de clínica para usar en la prueba} {--type= : Tipo específico de email (first, weekly, final). Si no se especifica, envía los 3 tipos} {--all : Enviar todas las plantillas de una vez}';
    protected $description = 'Enviar emails de prueba para verificar la configuración SMTP. Por defecto envía las 3 plantillas (first, weekly, final)';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $email = $this->argument('email');
        $codigo = $this->option('code');
        $tipo = $this->option('type');
        $enviarTodos = $this->option('all') || !$tipo; // Si no especifica tipo, envía todos
        
        $this->info("🧪 Probando envío de emails a: {$email}");
        $this->info("📋 Usando código de clínica: {$codigo}");
        
        if ($enviarTodos) {
            $this->info("📧 Se enviarán las 3 plantillas: first, weekly, final");
        } else {
            $this->info("📧 Se enviará solo la plantilla: {$tipo}");
        }
        
        // Verificar configuración primero
        $this->info('🔍 Verificando configuración SMTP...');
        $this->mostrarConfiguracion();
        
        if (!$this->confirm('¿Continuar con el envío?')) {
            $this->info('❌ Prueba cancelada');
            return Command::SUCCESS;
        }
        
        try {
            if ($enviarTodos) {
                return $this->enviarTodasLasPlantillas($email, $codigo);
            } else {
                return $this->enviarPlantillaEspecifica($email, $codigo, $tipo);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error inesperado: ' . $e->getMessage());
            $this->error('📋 Revisa los logs para más detalles');
            return Command::FAILURE;
        }
    }
    
    /**
     * Enviar todas las plantillas de email
     */
    private function enviarTodasLasPlantillas($email, $codigo)
    {
        $this->info('📤 Enviando todas las plantillas de email...');
        $this->newLine();
        
        $resultado = $this->emailService->enviarTodasLasPlantillasPrueba($email, $codigo);
        
        // Mostrar resultados detallados
        $this->info('📊 Resultados del envío:');
        $this->newLine();
        
        foreach ($resultado['detalles'] as $detalle) {
            $icono = $detalle['success'] ? '✅' : '❌';
            $tipoFormateado = ucfirst($detalle['tipo']);
            
            $this->line("{$icono} {$tipoFormateado}: {$detalle['message']}");
        }
        
        $this->newLine();
        
        if ($resultado['success']) {
            $this->info("🎉 ¡Todas las plantillas enviadas exitosamente!");
            $this->info("📧 Revisa tu bandeja de entrada y spam para ver los 3 emails");
            $this->info("📋 Total enviados: {$resultado['exitosos']}/{$resultado['total']}");
        } else {
            $this->warn("⚠️  Algunos emails fallaron:");
            $this->info("📋 Exitosos: {$resultado['exitosos']}/{$resultado['total']}");
            $this->info("📋 Errores: {$resultado['errores']}/{$resultado['total']}");
        }
        
        return $resultado['success'] ? Command::SUCCESS : Command::FAILURE;
    }
    
    /**
     * Enviar una plantilla específica
     */
    private function enviarPlantillaEspecifica($email, $codigo, $tipo)
    {
        $tiposValidos = ['first', 'weekly', 'final'];
        
        if (!in_array($tipo, $tiposValidos)) {
            $this->error("❌ Tipo de email inválido: {$tipo}");
            $this->info("📋 Tipos válidos: " . implode(', ', $tiposValidos));
            return Command::FAILURE;
        }
        
        $this->info("📤 Enviando plantilla '{$tipo}'...");
        
        $resultado = $this->emailService->enviarEmailPruebaTipo($email, $codigo, $tipo);
        
        if ($resultado['success']) {
            $this->info('✅ Email enviado exitosamente!');
            $this->info('📧 Revisa tu bandeja de entrada y spam');
        } else {
            $this->error('❌ Error: ' . $resultado['message']);
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
    
    private function mostrarConfiguracion()
    {
        $this->table(
            ['Configuración', 'Valor'],
            [
                ['MAIL_MAILER', env('MAIL_MAILER')],
                ['MAIL_HOST', env('MAIL_HOST')],
                ['MAIL_PORT', env('MAIL_PORT')],
                ['MAIL_ENCRYPTION', env('MAIL_ENCRYPTION', 'No configurado')],
                ['MAIL_USERNAME', env('MAIL_USERNAME')],
                ['MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')],
                ['MAIL_FROM_NAME', env('MAIL_FROM_NAME')],
            ]
        );
    }
}