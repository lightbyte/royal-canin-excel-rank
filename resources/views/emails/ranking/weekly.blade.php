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
        .variation {
            font-size: 1.2em;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin: 10px 0;
        }
        .variation.positive {
            background-color: #dcfce7;
            color: #166534;
        }
        .variation.negative {
            background-color: #fef2f2;
            color: #dc2626;
        }
        .variation.neutral {
            background-color: #f3f4f6;
            color: #374151;
        }
        .variation.new {
            background-color: #dbeafe;
            color: #1e40af;
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
        <p>Royal Canin - Semana {{ $semana }}</p>
    </div>
    
    <div class="content">
        <h2>¡Hola, Clínica {{ $codigo }}!</h2>
        
        <p>{{ $mensaje_principal }}</p>
        
        <div class="ranking-card">
            <h3>Tu Posición Actual</h3>
            <div class="position">Posición #{{ $posicion }}</div>
            <div class="points">{{ number_format($puntos) }} puntos</div>
            
            @if($variacion !== null)
                @if($variacion > 0)
                    <div class="variation positive">
                        ↗️ Subiste {{ $variacion }} {{ $variacion == 1 ? 'posición' : 'posiciones' }}!
                    </div>
                @elseif($variacion < 0)
                    <div class="variation negative">
                        ↘️ Bajaste {{ abs($variacion) }} {{ abs($variacion) == 1 ? 'posición' : 'posiciones' }}
                    </div>
                @else
                    <div class="variation neutral">
                        ➡️ Mantuviste tu posición
                    </div>
                @endif
            @else
                <div class="variation new">
                    🆕 ¡Nueva en el ranking!
                </div>
            @endif
            
            <p><strong>Fecha de actualización:</strong> {{ $fecha }}</p>
        </div>
        
        @if($variacion !== null && $variacion > 0)
            <div style="background-color: #dcfce7; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p><strong>🎉 ¡Felicitaciones!</strong> Has mejorado tu posición en el ranking. ¡Sigue así!</p>
            </div>
        @elseif($variacion !== null && $variacion < 0)
            <div style="background-color: #fef2f2; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p><strong>💪 ¡No te desanimes!</strong> Cada semana es una nueva oportunidad para mejorar. Contacta con tu representante para estrategias de mejora.</p>
            </div>
        @elseif($variacion === null)
            <div style="background-color: #dbeafe; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p><strong>🌟 ¡Bienvenida al ranking!</strong> Esta es tu primera aparición. ¡Esperamos verte crecer semana a semana!</p>
            </div>
        @endif
        
        <h3>Próximos pasos:</h3>
        <ul>
            <li>Consulta el ranking completo para ver tu competencia</li>
            <li>Revisa las estrategias con tu representante de Royal Canin</li>
            <li>Mantén el enfoque en las recomendaciones de calidad</li>
            <li>La próxima actualización será el próximo miércoles</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ env('APP_URL') }}/login" class="btn">Ver Ranking Completo</a>
        </div>
        
        <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <p><strong>🔑 Tu código de acceso:</strong> {{ $codigo }}</p>
            <p><small>Usa este código para acceder al sistema y consultar el ranking completo.</small></p>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>Royal Canin España</strong></p>
        <p>Este email ha sido enviado automáticamente. Si tienes alguna consulta, contacta con tu representante de Royal Canin.</p>
        <p><small>Fecha de envío: {{ $fecha }} | Semana: {{ $semana }}</small></p>
    </div>
</body>
</html>