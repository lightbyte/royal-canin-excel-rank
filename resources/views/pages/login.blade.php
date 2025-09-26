@extends('layouts.app')

@section('title', 'Iniciar Sesión - Ranking de Clínicas')
@section('page-title', 'Iniciar Sesión')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Acceso al Ranking</h2>
            <p class="text-gray-600">
                Introduce el código de tu clínica para acceder al ranking.
            </p>
        </div>
        
        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                    Código de Clínica
                </label>
                <input type="text" 
                       id="codigo" 
                       name="codigo" 
                       value="{{ old('codigo') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo') border-red-500 @enderror"
                       placeholder="Ej: CLI001"
                       required
                       autocomplete="off">
                
                @error('codigo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Acceder al Ranking
            </button>
        </form>
        
        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-500 mb-2">
                ¿No tienes tu código de clínica?
            </p>
            <p class="text-xs text-gray-400">
                Contacta con tu representante de Royal Canin para obtenerlo.
            </p>
        </div>
    </div>
</div>
@endsection