<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ranking de Clínicas')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    <!-- Header -->
    <header class="py-6">
        <div class="mx-auto px-2 sm:px-14">
            <div class="text-center p-4 block sm:hidden">
                <img src="{{ asset('images/logo-royal-canin.png') }}" alt="Royal Canin" class="mx-auto">
            </div>
            <div class="flex flex-row justify-between items-center gap-2 sm:gap-0">
                @if (!(request()->routeIs('home') || request()->routeIs('pt.home')))
                    @php
                        $backUrl = "history.back()";
                        if (request()->routeIs('thanks')){
                            $backUrl = "window.location='" . route('home') . "'";
                        }
                        if (request()->routeIs('pt.thanks')){
                            $backUrl = "window.location='" . route('pt.home') . "'";
                        }
                    @endphp


                    <button onclick="{{ $backUrl }}"
                        type="button" 
                        class="btn-back bg-royal-red text-white text-center w-[70px] h-[70px] mr-4 rounded-full hover:bg-opacity-90">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div class="header-text bg-royal-red text-white leading-none text-center py-2 px-4 mr-0 sm:mr-4 rounded-2xl">
                        <span>@yield('header_title')</span>
                    </div>
                @else
                    <a href="#saber-mas" class="header-text bg-royal-red text-white leading-none text-center py-2 px-4 mr-0 sm:mr-4 rounded-2xl">
                        <span>{!! __('public.header_btn_1') !!}</span>
                    </a>
                    <a href="{{ route('ranking') }}" class="header-text bg-royal-red text-white leading-none text-center py-2 px-4 mr-0 sm:mr-4 rounded-2xl">
                        <span>{!! __('public.header_btn_2') !!}</span>
                    </a>
                    <a href="{{ route('login') }}" class="header-text bg-royal-red text-white leading-none text-center py-2 px-4 mr-0 sm:mr-4 rounded-2xl">
                        <span>{!! __('public.header_btn_3') !!}</span>
                    </a>
                @endif
                <div class="header-line bg-royal-red grow shrink hidden sm:block"></div>
                <div class="text-center p-4 ml-4 hidden sm:block">
                    <img src="{{ asset('images/logo-royal-canin.png') }}" alt="Royal Canin" class="logo mx-auto mt-[-20px] pb-[20px]">
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
        </div>
        
        @yield('content')
    </main>

</body>
</html>