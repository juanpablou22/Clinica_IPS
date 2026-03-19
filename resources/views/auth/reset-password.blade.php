{{-- 
    ARCHIVO: resources/views/auth/reset-password.blade.php
    DESCRIPCIÓN: Formulario final para establecer una nueva contraseña tras solicitar recuperación.
    ESTÉTICA: Hereda el diseño de tarjeta dividida de 'x-guest-layout'.
--}}

<x-guest-layout>
    {{-- 
        FORMULARIO DE RESTABLECIMIENTO:
        Envía los datos a la ruta 'password.store' mediante POST para actualizar la DB.
    --}}
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- 
            TOKEN DE SEGURIDAD:
            Campo oculto que contiene el token único enviado al correo del usuario.
            Es indispensable para validar que la petición de cambio sea legítima.
        --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- CAMPO: CORREO ELECTRÓNICO --}}
        <div>
            {{-- Se recomienda cambiar 'Email' por 'Correo Institucional' o similar --}}
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            
            {{-- 
                Muestra el email que viene en la URL de recuperación por defecto.
                El atributo 'readonly' podría añadirse aquí si no quieres que lo cambien.
            --}}
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- CAMPO: NUEVA CONTRASEÑA --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Nueva Contraseña')" />
            
            <x-text-input id="password" class="block mt-1 w-full" 
                            type="password" 
                            name="password" 
                            required autocomplete="new-password" 
                            placeholder="Mínimo 8 caracteres" />
            
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- CAMPO: CONFIRMAR CONTRASEÑA --}}
        <div class="mt-4">
            {{-- Es vital que este campo se llame 'password_confirmation' para que Laravel lo valide automáticamente --}}
            <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" 
                                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- BOTÓN DE ACCIÓN --}}
        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full md:w-auto justify-center">
                {{ __('Restablecer Contraseña') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>