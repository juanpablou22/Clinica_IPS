{{-- 
    SISTEMA DE GESTIÓN - I.P.S CREAR INTEGRAL S.A.S
    Layout: Guest (Invitado)
    Desarrollado por: SnakeDEV
    Descripción: Estructura de pantalla dividida (Split Screen). 
    Lado izquierdo para Branding y lado derecho para formularios dinámicos.
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Fuentes e Iconos --}}
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Scripts y Estilos procesados por Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-100">
        
        {{-- Contenedor principal centrado --}}
        <div class="min-h-screen flex items-center justify-center p-4">
            
            {{-- Tarjeta Principal: 'max-w-5xl' para un ancho profesional y 'rounded-3xl' para bordes modernos --}}
            <div class="flex flex-col md:flex-row bg-white shadow-2xl rounded-3xl overflow-hidden max-w-5xl w-full min-h-[550px]">
                
                {{-- LADO IZQUIERDO: IDENTIDAD VISUAL --}}
                <div class="md:w-1/2 bg-gradient-to-br from-blue-50 to-white flex flex-col items-center justify-center p-12 border-b md:border-b-0 md:border-r border-gray-100">
                    <div class="transition-all duration-700 hover:scale-110">
                        <a href="/">
                            <x-application-logo class="w-full h-auto" />
                        </a>
                    </div>
                    <div class="mt-8 text-center">
                        <h2 class="text-xl font-semibold text-slate-700 uppercase tracking-wider italic">
                            IPS Crear Integral S.A.S
                        </h2>
                        <p class="text-slate-400 text-xs mt-1">Gestión Médica Profesional</p>
                    </div>
                </div>

                {{-- LADO DERECHO: FORMULARIOS (LOGIN / REGISTRO) --}}
                <div class="md:w-1/2 p-8 md:p-16 flex flex-col justify-center">
                    
                    {{-- SLOT DE ENCABEZADO: Si la vista hija define un <x-slot name="header">, se muestra aquí --}}
                    @if (isset($header))
                        <div class="mb-8 border-l-4 border-blue-500 pl-4">
                            {{ $header }}
                        </div>
                    @endif

                    {{-- CONTENIDO PRINCIPAL: Aquí se inyecta el formulario --}}
                    {{ $slot }}

                    {{-- PIE DE PÁGINA: Créditos del sistema --}}
                    <div class="mt-12 text-center border-t border-gray-50 pt-6">
                        <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">
                            &copy; {{ date('Y') }} SnakeDEV - Tecnología para la Salud
                        </p>
                    </div>
                </div>

            </div> {{-- Fin Tarjeta --}}
        </div> {{-- Fin Contenedor --}}
    </body>
</html>