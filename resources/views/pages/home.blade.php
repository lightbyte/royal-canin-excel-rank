@extends('layouts.app')

@section('title', 'Inicio - Ranking de Clínicas')
@section('page-title', 'Ranking de Clínicas Royal Canin')

@section('content')
<div class="container mx-auto px-4">
    <div class="text-center">
        <h1 class="font-bold text-royal-red mb-20">BIENVENIDOS AL RANKING DE LAS MEJORES CLÍNICAS EN ASESORAMIENTO NUTRICIONAL A TRAVÉS DE VET SERVICES</h1>
        
        <h2 class="font-bold text-royal-red mb-30">¡Tu clínica ya forma parte!</h2>

        <div class="max-w-[1400px] mx-auto">
            <p class="text-royal-dark mb-10 ">
                Queremos felicitarte e incluirte en este ranking porque tu clínica está entre las mejores de España en el uso de la herramienta de asesoramiento nutricional Vet Services.
            </p>
            <p class="text-royal-dark mb-10">
                Gracias a tu esfuerzo y dedicación, has marcado la diferencia en el cuidado y bienestar de tus pacientes, y tu participación es el reflejo de ese compromiso.
            </p>
        </div>
    </div>
</div>

<div class="trophy">

    <div id="saber-mas" class="container mx-auto px-4 py-8 prizes">
        <h2 class="mb-20">
            ¡Tu clínica puede estar en lo más alto!
        </h2>
        
        <div class="flex flex-col md:flex-row sm:justify-start mb-16">
            <div class="flex flex-row items-start gap-12">
                <div class="prize-icon">
                    <img src="{{ asset('images/icon-podium.svg') }}" alt="Podio">
                </div>
                <div class="prize-text">
                    <h3 class="text-royal-dark mb-6">
                        ¿CÓMO GANAS PUNTOS?
                    </h3>
                    <div class="prizes-description">
                        <p class="trophy-parragraph mb-6">
                            Durante <b>octubre y noviembre</b>, cada vez que realices una recomendación a través de cualquiera de las herramientas de Vet Services (Ración Diaria, Smart Reco, Programa de Peso) ganarás un punto.
                        </p>
                        
                        <p class="trophy-parragraph"><b>¡Cada reco cuenta!</b></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row sm:justify-start mb-6">
            <div class="flex flex-row items-start gap-12">
                <div class="prize-icon">
                    <img src="{{ asset('images/icon-medall.svg') }}" alt="Medalla">
                </div>
                <div class="prize-text">
                    <h3 class="text-royal-dark  mb-6">
                        ¿QUÉ PUEDES CONSEGUIR?
                    </h3>
                    <div class="prizes-description">
                        <p class="trophy-parragraph mb-6">
                            Si realizas al menos <b>50 recomendaciones</b> a través de Vet Services entre Octubre y Noviembre recibirás una <b>tarjeta regalo de 100€</b>.
                        </p>
                        <p class="trophy-parragraph mb-6">
                            Y si eres una de las <b>tres clínicas con más recomendaciones</b>, ¡te llevas otra <b>tarjeta adicional de 250€!</b>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col md:flex-row sm:justify-start mb-8 sm:pl-18">
            <a href="{{ route('ranking') }}" class="bg-royal-red text-white px-4 sm:px-12 py-4 text-center rounded-full font-bold">
                DESCUBRE TU POSICIÓN EN EL RANKING AQUÍ
            </a>
        </div>

    </div>

</div>

<div class="mx-auto mt-30 px-4 sm:px-14">
    <div class="">
        <p class="mb-2 font-bold">
            Condiciones para optar a premio:
        </p>
        <p class="mb-8">
            1. Estar registrado en la plataforma. <br>
            2. Haber compartido al menos una recomendación profesional durante el periodo de medición.
        </p>
    </div>

    <div class="footer-text mt-30 flex flex-col md:flex-row justify-start md:justify-between md:items-end">
        <div class="mb-8 max-w-[1000px]">
            *Promoción disponible temporalmente. Consulta con tu agente comercial. Suma de recomendaciones de octubre y noviembre. Las tarjetas regalo se emitirán en diciembre 2025. Sólo para clínicas seleccionadas como participantes en la promoción.
        </div>
        <div class="mb-8">
            <a href="{{ route('privacy') }}" class="hover:text-royal-dark transition-colors hover:underline">
                *Política de privacidad
            </a>
        </div>
    </div>
</div>
@endsection