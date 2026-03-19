<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'I.P.S Crear Integral') }}</title>
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50" x-data="{ sidebarOpen: true }">
        <div class="min-h-screen flex overflow-hidden">
            
            <aside 
                :class="sidebarOpen ? 'w-64' : 'w-20'" 
                class="bg-white border-r border-slate-200 transition-all duration-300 ease-in-out flex flex-col flex-shrink-0 shadow-sm z-20"
                x-cloak>
                @include('layouts.navigation')
            </aside>

            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                
                <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 shadow-sm z-10">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-blue-600 focus:outline-none p-2 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </button>
                        
                        <div class="flex items-center justify-center" style="width: 150px; height: 50px;">
                            <img src="{{ asset('LOGIN.png') }}" alt="Logo" class="w-full h-full object-contain pointer-events-none">
                        </div>
                    </div>

                    @isset($header)
                        <div class="hidden md:block font-semibold text-slate-700">
                            {{ $header }}
                        </div>
                    @endisset
                </header>

                <main class="flex-1 overflow-y-auto p-6 bg-slate-50">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>