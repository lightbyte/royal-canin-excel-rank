@extends('layouts.app')

@section('title', 'Política de Privacidad - Ranking de Clínicas')
@section('page-title', 'Política de Privacidad')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="prose max-w-none">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Política de Privacidad</h2>
            
            <div class="space-y-6 text-gray-700">
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">1. Información que Recopilamos</h3>
                    <p class="mb-4">
                        En el marco del sistema de ranking de clínicas de Royal Canin, recopilamos únicamente la información necesaria para el funcionamiento del sistema:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li>Código de identificación de la clínica</li>
                        <li>Dirección de correo electrónico de contacto</li>
                        <li>Datos de recomendaciones y puntuación</li>
                        <li>Información de navegación básica (cookies técnicas)</li>
                    </ul>
                </section>
                
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">2. Uso de la Información</h3>
                    <p class="mb-4">
                        La información recopilada se utiliza exclusivamente para:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li>Mostrar el ranking de clínicas participantes</li>
                        <li>Enviar notificaciones sobre actualizaciones del ranking</li>
                        <li>Proporcionar acceso personalizado al sistema</li>
                        <li>Mejorar la experiencia del usuario</li>
                    </ul>
                </section>
                
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">3. Protección de Datos</h3>
                    <p class="mb-4">
                        Nos comprometemos a proteger su información personal mediante:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li>Medidas de seguridad técnicas y organizativas apropiadas</li>
                        <li>Acceso restringido a la información solo al personal autorizado</li>
                        <li>Encriptación de datos sensibles</li>
                        <li>Copias de seguridad regulares y seguras</li>
                    </ul>
                </section>
                
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">4. Compartir Información</h3>
                    <p>
                        No compartimos, vendemos ni alquilamos su información personal a terceros. 
                        La información del ranking es visible únicamente para las clínicas participantes 
                        en el programa de Royal Canin.
                    </p>
                </section>
                
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">5. Retención de Datos</h3>
                    <p>
                        Conservamos su información personal durante el período necesario para cumplir 
                        con los propósitos descritos en esta política, o según lo requiera la ley aplicable.
                    </p>
                </section>
                
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">6. Sus Derechos</h3>
                    <p class="mb-4">
                        Usted tiene derecho a:
                    </p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li>Acceder a su información personal</li>
                        <li>Rectificar datos inexactos</li>
                        <li>Solicitar la eliminación de sus datos</li>
                        <li>Oponerse al procesamiento de sus datos</li>
                        <li>Solicitar la portabilidad de sus datos</li>
                    </ul>
                </section>
                
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">7. Cookies</h3>
                    <p>
                        Utilizamos cookies técnicas estrictamente necesarias para el funcionamiento 
                        del sistema de autenticación y navegación. Estas cookies no requieren 
                        consentimiento específico.
                    </p>
                </section>
                
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">8. Contacto</h3>
                    <p class="mb-4">
                        Para cualquier consulta relacionada con esta política de privacidad o 
                        para ejercer sus derechos, puede contactarnos:
                    </p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p><strong>Royal Canin España</strong></p>
                        <p>Email: {{ env('MAIL_FROM_ADDRESS', 'info@agenciamarsway.com') }}</p>
                        <p>Teléfono: [Número de contacto]</p>
                    </div>
                </section>
                
                <section>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">9. Cambios en esta Política</h3>
                    <p>
                        Nos reservamos el derecho de actualizar esta política de privacidad. 
                        Los cambios significativos serán comunicados a través del sistema 
                        o por correo electrónico.
                    </p>
                </section>
                
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        <strong>Última actualización:</strong> {{ date('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection