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
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 30px 20px;
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
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #1e40af;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .final-position {
            font-size: 2.5em;
            font-weight: bold;
            color: #1e40af;
            text-align: center;
        }
        .final-points {
            font-size: 1.8em;
            color: #059669;
            font-weight: bold;
            text-align: center;
        }
        .medal {
            font-size: 3em;
            text-align: center;
            margin: 20px 0;
        }
        .achievement {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
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
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .stat-item {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .stat-number {
            font-size: 1.5em;
            font-weight: bold;
            color: #1e40af;
        }
        .stat-label {
            font-size: 0.9em;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p>Royal Canin - Período 2025</p>
        <p><strong>¡Gracias por tu participación!</strong></p>
    </div>
    
    <div class="content">
        <h2>¡Hola, Clínica {{ $codigo }}!</h2>
        
        <p>{{ $mensaje_principal }}</p>
        
        <p>Ha sido un placer acompañarte durante todo este período y ver tu evolución en el ranking de clínicas Royal Canin.</p>
        
        <div class="ranking-card">
            <h3>Tu Posición Final</h3>
            
            @if($posicion_final <= 3)
                <div class="medal">
                    @if($posicion_final == 1) 🥇
                    @elseif($posicion_final == 2) 🥈
                    @elseif($posicion_final == 3) 🥉
                    @endif
                </div>
            @endif
            
            <div class="final-position">Posición #{{ $posicion_final }}</div>
            <div class="final-points">{{ number_format($puntos_totales) }} puntos totales</div>
            
            @if($posicion_final <= 3)
                <div class="achievement">
                    <h4>🎉 ¡FELICITACIONES! 🎉</h4>
                    <p>Has terminado en el <strong>TOP 3</strong> del ranking. ¡Un logro excepcional!</p>
                </div>
            @elseif($posicion_final <= 10)
                <div class="achievement">
                    <h4>🌟 ¡EXCELENTE TRABAJO! 🌟</h4>
                    <p>Has terminado en el <strong>TOP 10</strong> del ranking. ¡Muy buen desempeño!</p>
                </div>
            @endif
        </div>
        
        <h3>Resumen de tu participación:</h3>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $posicion_final }}</div>
                <div class="stat-label">Posición Final</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ number_format($puntos_totales) }}</div>
                <div class="stat-label">Puntos Totales</div>
            </div>
        </div>
        
        <h3>Lo que has logrado:</h3>
        <ul>
            <li>✅ Participaste activamente en el programa de ranking</li>
            <li>✅ Contribuiste al crecimiento de las recomendaciones Royal Canin</li>
            <li>✅ Formaste parte de una comunidad comprometida con la excelencia</li>
            @if($posicion_final <= 10)
                <li>✅ Alcanzaste una posición destacada en el ranking</li>
            @endif
            @if($posicion_final <= 3)
                <li>✅ Te posicionaste entre los mejores del período</li>
            @endif
        </ul>
        
        <div style="background-color: #dbeafe; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4>🙏 Agradecimiento especial</h4>
            <p>Queremos agradecerte por tu dedicación y compromiso durante todo este período. Tu participación ha sido fundamental para el éxito del programa.</p>
        </div>
        
        <h3>¿Qué sigue ahora?</h3>
        <ul>
            <li>Mantén el excelente trabajo con las recomendaciones Royal Canin</li>
            <li>Continúa brindando el mejor servicio a tus clientes</li>
            <li>Estate atento a futuros programas y oportunidades</li>
            <li>Sigue en contacto con tu representante de Royal Canin</li>
        </ul>
        
        <div style="text-align: center;">
            <a href="{{ env('APP_URL') }}/login" class="btn">Ver Ranking Final Completo</a>
        </div>
        
        <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
            <h4>🎯 ¡Hasta la próxima!</h4>
            <p>Esperamos verte en futuros programas de Royal Canin. ¡Gracias por ser parte de nuestra familia!</p>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>Royal Canin España</strong></p>
        <p>Gracias por tu participación en el Ranking de Clínicas 2025</p>
        <p><small>Fecha de cierre: {{ $fecha }}</small></p>
    </div>
</body>
</html>