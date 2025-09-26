@extends('layouts.app')

@section('title', 'Inicio - Ranking de Clínicas')
@section('page-title', 'Ranking de Clínicas Royal Canin')

@section('content')
<div class="text-center">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Bienvenido</h2>
            <p class="text-gray-600 mb-6">
                Consulta tu posición en el ranking de clínicas y mantente al día con tu progreso.
            </p>
        </div>
        
        <div class="space-y-4">
            @if(session('clinic_code'))
                <!-- Si ya está logueado, ir directo al ranking -->
                <a href="{{ route('ranking') }}" 
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors inline-block">
                    Ver Ranking
                </a>
                
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        Cambiar de Clínica
                    </button>
                </form>
            @else
                <!-- Si no está logueado, mostrar opciones -->
                <a href="{{ route('login') }}" 
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors inline-block">
                    Iniciar Sesión
                </a>
                
                <a href="{{ route('ranking') }}" 
                   class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors inline-block">
                    Ver Ranking
                </a>
            @endif
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500">
                ¿Necesitas ayuda? Contacta con tu representante de Royal Canin.
            </p>
        </div>
    </div>
</div>
@endsection