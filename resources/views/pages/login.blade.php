@extends('layouts.app')

@section('title', 'Iniciar Sesión - Ranking de Clínicas')
@section('page-title', 'Iniciar Sesión')
@section('header_title', 'Registro')

@section('content')
<div class="trophy">

    <div class="container max-w-[800px] mx-auto px-2 lg:px-16 py-8">
        <h1 class="login-title mb-20">
            Introduce tu código de cliente para acceder al ranking
        </h1>
        
        <div class="contenido-form">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <div class="field-group bg-royal-red rounded-lg py-2 px-6 flex items-center">
                        <img src="{{ asset('images/icon-pencil.svg') }}" alt="Logo" class="w-12 h-12 mr-2">
                        <input type="text" 
                            id="codigo" 
                            name="codigo" 
                            value="{{ old('codigo') }}"
                            class="w-full px-3 py-2 no-border focus:outline-none focus:ring-0 bg-transparent text-white placeholder-white"
                            placeholder="Código cliente"
                            required
                            autocomplete="off">
                    </div>
                    
                    @error('codigo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="text-sm text-gray-500 mt-2 text-right">
                        * Puedes encontrar tu código de cliente en las facturas o consultar a tu agente comercial.
                    </p>
                </div>
                
                <div class="text-center">
                    <button type="submit" 
                            class="w-auto bg-royal-red hover:bg-royal-dark text-white font-medium py-3 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-royal-dark focus:ring-offset-2">
                        Acceder al Ranking
                    </button>
                </div>
            </form>
        </div>
            
    </div>
    

</div>


<div class="mx-auto mt-30 px-4 sm:px-14">
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