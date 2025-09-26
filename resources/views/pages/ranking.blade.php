@extends('layouts.app')

@section('title', 'Ranking - Ranking de Clínicas')
@section('page-title', 'Ranking de Clínicas')

@section('content')
<div class="space-y-6">
    <!-- Filtro de búsqueda -->
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <label for="filtro-codigo" class="block text-sm font-medium text-gray-700 mb-2">
                    Buscar por código de clínica
                </label>
                <input type="text" 
                       id="filtro-codigo" 
                       placeholder="Introduce el código para buscar..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button id="btn-limpiar" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                Limpiar
            </button>
        </div>
    </div>
    
    @if($clinicaLogueada)
        <!-- Información de la clínica logueada -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-900">Tu Posición Actual</h3>
                    <p class="text-blue-700">
                        Clínica: <strong>{{ $clinicaLogueada->codigo }}</strong> - 
                        Posición: <strong>#{{ $clinicaLogueada->posicion_actual }}</strong> - 
                        Puntos: <strong>{{ $clinicaLogueada->recomendaciones }}</strong>
                        @if($clinicaLogueada->variacion !== null)
                            - Variación: 
                            <span class="@if($clinicaLogueada->variacion > 0) text-green-600 @elseif($clinicaLogueada->variacion < 0) text-red-600 @else text-gray-600 @endif font-semibold">
                                {{ $clinicaLogueada->variacion_formateada }}
                            </span>
                        @endif
                    </p>
                </div>
                <button id="btn-ir-mi-posicion" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Ir a mi posición
                </button>
            </div>
        </div>
    @endif
    
    <!-- Tabla de ranking -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="tabla-ranking">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Posición
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Código de Clínica
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Puntos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Variación
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rankings as $ranking)
                        <tr class="hover:bg-gray-50 transition-colors ranking-row" 
                            data-codigo="{{ strtolower($ranking->codigo) }}"
                            @if($clinicaLogueada && $ranking->codigo === $clinicaLogueada->codigo) 
                                id="mi-clinica" 
                            @endif>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-900">
                                        #{{ $ranking->posicion_actual }}
                                    </span>
                                    @if($ranking->posicion_actual <= 3)
                                        <span class="ml-2">
                                            @if($ranking->posicion_actual == 1) 🥇
                                            @elseif($ranking->posicion_actual == 2) 🥈
                                            @elseif($ranking->posicion_actual == 3) 🥉
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $ranking->codigo }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ number_format($ranking->recomendaciones) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ranking->variacion !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($ranking->variacion > 0) bg-green-100 text-green-800
                                        @elseif($ranking->variacion < 0) bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($ranking->variacion > 0)
                                            ↗️ +{{ $ranking->variacion }}
                                        @elseif($ranking->variacion < 0)
                                            ↘️ {{ $ranking->variacion }}
                                        @else
                                            ➡️ {{ $ranking->variacion }}
                                        @endif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        🆕 NEW
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No hay datos de ranking disponibles.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($rankings->count() > 0)
        <div class="text-center text-sm text-gray-500">
            Total de clínicas: {{ $rankings->count() }}
        </div>
    @endif
</div>

<script>
// Funcionalidad de filtro y scroll
document.addEventListener('DOMContentLoaded', function() {
    const filtroInput = document.getElementById('filtro-codigo');
    const btnLimpiar = document.getElementById('btn-limpiar');
    const btnIrMiPosicion = document.getElementById('btn-ir-mi-posicion');
    const filas = document.querySelectorAll('.ranking-row');
    
    // Función de filtro
    function filtrarTabla() {
        const filtro = filtroInput.value.toLowerCase().trim();
        
        filas.forEach(fila => {
            const codigo = fila.dataset.codigo;
            if (codigo.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }
    
    // Función para ir a mi posición
    function irAMiPosicion() {
        const miClinica = document.getElementById('mi-clinica');
        if (miClinica) {
            miClinica.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            // Resaltar temporalmente
            miClinica.classList.add('bg-blue-100');
            setTimeout(() => {
                miClinica.classList.remove('bg-blue-100');
            }, 2000);
        }
    }
    
    // Event listeners
    filtroInput.addEventListener('input', filtrarTabla);
    
    btnLimpiar.addEventListener('click', function() {
        filtroInput.value = '';
        filtrarTabla();
    });
    
    if (btnIrMiPosicion) {
        btnIrMiPosicion.addEventListener('click', irAMiPosicion);
        
        // Auto-scroll al cargar la página
        setTimeout(irAMiPosicion, 500);
    }
});
</script>
@endsection