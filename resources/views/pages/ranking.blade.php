@extends('layouts.app')

@section('title', 'Ranking - Ranking de Clínicas')
@section('page-title', 'Ranking de Clínicas')
@section('header_title', 'Ranking')

@section('content')
<div class="container mx-auto px-4">

    <div class="text-center">
        <h1 class="mb-15">¡TU CLÍNICA PUEDE ESTAR EN LO MÁS ALTO!</h1>
        <p class="mb-15">Sigue de cerca la clasificación actual y sube puestos con cada punto que ganes</p>
        <p class="mb-20 font-bold">1 reco = 1 punto
    </div>

    <!-- Filtro de búsqueda -->
    <div class="mb-4">
        <div class="flex flex-col sm:flex-row items-center justify-between space-x-4">
            <div class="">
                <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="text-royal-red hover:text-royal-dark font-medium">
                Cerrar sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
            <div class="field-group bg-gray-200 rounded-xl py-1 px-6 flex items-center">
                <input type="text" 
                       id="filtro-codigo" 
                       placeholder="BUSCA TU CLÍNICA"
                       class="w-full px-1 py-2 no-border focus:outline-none focus:ring-0 bg-transparent text-grey-800 placeholder-grey-800">
                <img src="{{ asset('images/icon-search.svg') }}" alt="Buscar" class="w-8 h-8 ml-2">
            </div>
        </div>
    </div>
    
    <!-- Tabla de ranking -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <div class="bg-royal-red sm:px-10">
                <table class="min-w-full" id="tabla-ranking">
                    <thead class="bg-royal-red">
                        <tr>
                            <th class="w-[25%] px-6 py-8 text-center text-white uppercase tracking-wider">
                                Posición
                            </th>
                            <th class="w-[25%] px-6 py-8 text-center text-white uppercase tracking-wider">
                                Clínica
                            </th>
                            <th class="w-[25%] px-6 py-8 text-center text-white uppercase tracking-wider">
                                Puntos
                            </th>
                            <th class="w-[25%] px-6 py-8 text-center text-white uppercase tracking-wider">
                                Cambios
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="max-h-[600px] overflow-y-auto bg-royal-grey sm:p-10">
                <table class="min-w-full">
                    <tbody class="divide-y divide-gray-400">
                        @forelse($rankings as $ranking)
                            <tr class="bg-royal-grey transition-colors ranking-row" 
                                data-codigo="{{ strtolower($ranking->codigo) }}"
                                @if($clinicaLogueada && $ranking->codigo === $clinicaLogueada->codigo) 
                                    id="mi-clinica" 
                                @endif>
                                <td class="w-[25%] px-6 py-4 whitespace-nowrap">
                                    <div class="mx-auto flex items-center justify-center bg-royal-red text-white rounded-full w-[40px] h-[40px] sm:w-[70px] sm:h-[70px]">
                                        <span>
                                            {{ $ranking->posicion_actual }}
                                        </span>
                                    </div>
                                </td>
                                <td class="w-[25%] px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900">{{ $ranking->codigo }}</div>
                                </td>
                                <td class="w-[25%] px-6 py-4 whitespace-nowrap">
                                    <div class="text-center text-gray-900">{{ number_format($ranking->recomendaciones) }}</div>
                                </td>
                                <td class="w-[25%] px-6 py-4 whitespace-nowrap">
                                    @if($ranking->variacion !== null)
                                        <div class="flex flex-row items-center justify-center text-royal-red">
                                            @if($ranking->variacion > 0)
                                                <svg class="w-16 h-16 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" transform="rotate(90 12 12)"></path>
                                                </svg> <span class="w-[40px] text-right">+{{ $ranking->variacion }}</span>
                                            @elseif($ranking->variacion < 0)
                                                <svg class="w-16 h-16 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" transform="rotate(-90 12 12)"></path>
                                                </svg> <span class="w-[40px] text-right">{{ $ranking->variacion }}</span>
                                            @else
                                                <svg class="w-16 h-16 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                                                </svg> <span class="w-[40px] text-right">{{ $ranking->variacion }}</span>
                                            @endif
                                        </spadivn>
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
    </div>
    
    <div class="mt-30 flex flex-col sm:flex-row items-start justify-between max-w-full">
        <h2 class="title-footer text-royal-red sm:pt-10">PARTICIPA MÁS PARA SUBIR POSICIONES</h2>
        <div class="">
            <img src="{{ asset('images/podium-cup.png') }}" alt="Podium" class="max-w-full">
        </div>
    </div>

</div>

<div class="mx-auto mt-30 px-14">
    <div class="footer-text mt-30 flex flex-col md:flex-row justify-start md:justify-between md:items-end">
        <div class="mb-8"></div>
        <div class="mb-8">
            <a href="{{ route('privacy') }}" class="hover:text-royal-dark transition-colors hover:underline">
                *Política de privacidad
            </a>
        </div>
    </div>
</div>
@endsection