{{-- 
    VISTA: Login (Inicio de Sesión)
    Hereda de: x-guest-layout
--}}

<x-guest-layout>
    {{-- Definimos el encabezado dinámico para esta página --}}
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-800">Bienvenido</h1>
        <p class="text-slate-500 mt-1 text-sm">Ingrese sus credenciales para continuar.</p>
    </x-slot>

    {{-- Estado de la sesión (ej: mensajes de éxito al resetear clave) --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Recordarme y Olvido de clave --}}
        <div class="flex items-center justify-between mt-6">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm">
                <span class="ms-2 text-xs text-gray-600">Recordarme</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="text-xs text-gray-600 underline hover:text-blue-600 transition-colors" href="{{ route('password.request') }}">
                    ¿Olvidó su contraseña?
                </a>
            @endif
        </div>

        {{-- Botón de Acción Principal --}}
        <div class="mt-8">
            <x-primary-button class="w-full justify-center py-3 bg-slate-800 hover:bg-slate-900 shadow-lg">
                {{ __('INICIAR SESIÓN') }}
            </x-primary-button>
        </div>

        {{-- Link hacia el Registro --}}
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-slate-500">
                ¿No tienes una cuenta? 
                <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:underline">
                    Nuevo Usuario
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>