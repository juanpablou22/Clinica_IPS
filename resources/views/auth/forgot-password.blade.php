{{-- 
    ARCHIVO: resources/views/auth/forgot-password.blade.php
    DESCRIPCIÓN: Vista de solicitud de recuperación de contraseña.
    FUNCIONAMIENTO: Envía un correo electrónico con un token de seguridad único 
                   al usuario para validar su identidad.
--}}

<x-guest-layout>
    {{-- 
        MENSAJE INFORMATIVO:
        Explica al usuario el proceso de recuperación. 
        Se recomienda traducir el texto dentro de {{ __('...') }} para la IPS.
    --}}
    <div class="mb-4 text-sm text-gray-600 leading-relaxed">
        {{ __('¿Olvidó su contraseña? No hay problema. Ingrese su dirección de correo electrónico institucional y le enviaremos un enlace para restablecerla que le permitirá elegir una nueva.') }}
    </div>

    {{-- 
        ESTADO DE LA SESIÓN:
        Muestra el mensaje de éxito una vez que el correo ha sido enviado 
        (ej: "Hemos enviado el enlace a su correo").
    --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- 
        FORMULARIO DE SOLICITUD:
        Apunta a la ruta 'password.email' encargada de gestionar el envío de correos.
    --}}
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- CAMPO: CORREO ELECTRÓNICO --}}
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            
            {{-- 
                Input optimizado para capturar el email. 
                'autofocus' permite que el usuario empiece a escribir de inmediato.
            --}}
            <x-text-input id="email" class="block mt-1 w-full" 
                            type="email" 
                            name="email" 
                            :value="old('email')" 
                            required 
                            autofocus 
                            placeholder="usuario@ipscrear.com" />

            {{-- Manejo de errores (ej: Si el correo no existe en la base de datos) --}}
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- BOTÓN DE ENVÍO --}}
        <div class="flex items-center justify-end mt-6">
            {{-- 
                El x-primary-button enviará la petición al servidor.
                Se sugiere un texto más corto y directo para la estética del botón.
            --}}
            <x-primary-button class="w-full md:w-auto justify-center">
                {{ __('Enviar Enlace de Recuperación') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
