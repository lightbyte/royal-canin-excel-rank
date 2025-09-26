<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $asunto }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1e40af;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .ranking-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #1e40af;
        }
        .position {
            font-size: 2em;
            font-weight: bold;
            color: #1e40af;
        }
        .points {
            font-size: 1.5em;
            color: #059669;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.9em;
        }
        .btn {
            display: inline-block;
            background-color: #1e40af;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Royal Canin - Sistema de Ranking</p>
    </div>
    
    <div class="content">
        <h2>¡Hola, Clínica {{ $codigo }}!</h2>
        
        <p>{{ $mensaje_principal }}</p>
        
        <p>A partir de hoy, podrás consultar tu posición en el ranking y seguir tu progreso semana a semana.</p>
        
        <div class="ranking-card">
            <h3>Tu Posición Inicial</h3>
            <div class="position">Posición #{{ $posicion }}</div>
            <div class="points">{{ number_format($puntos) }} puntos</div>
            <p><strong>Fecha:</strong> {{ $fecha }}</p>
        </div>
        
        <h3>¿Cómo funciona el sistema?</h3>
        <ul>
            <li><strong>Actualizaciones semanales:</strong> Cada miércoles actualizamos el ranking</li>
            <li><strong>Notificaciones:</strong> Te enviaremos un email con tu nueva posición</li>
            <li><strong>Acceso online:</strong> Puedes consultar el ranking completo en cualquier momento</li>
            <li><strong>Tu código:</strong> Usa el código <strong>{{ $codigo }}</strong> para acceder al sistema</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ env('APP_URL') }}" class="btn">Acceder al Ranking</a>
        </div>
        
        <h3>Consejos para mejorar tu posición:</h3>
        <ul>
            <li>Aumenta las recomendaciones de productos Royal Canin</li>
            <li>Mantén un seguimiento constante de tus ventas</li>
            <li>Consulta regularmente tu posición en el ranking</li>
            <li>Contacta con tu representante para estrategias personalizadas</li>
        </ul>
        
        <div style="background-color: #dbeafe; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <p><strong>💡 Recuerda:</strong> Tu código de acceso es <strong>{{ $codigo }}</strong>. Guárdalo bien, lo necesitarás para consultar el ranking.</p>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>Royal Canin España</strong></p>
        <p>Este email ha sido enviado automáticamente. Si tienes alguna consulta, contacta con tu representante de Royal Canin.</p>
        <p><small>Fecha de envío: {{ $fecha }}</small></p>
    </div>
</body>
</html>