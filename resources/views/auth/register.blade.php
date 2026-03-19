{{-- 
    VISTA: Registro (Crear Cuenta)
    Hereda de: x-guest-layout
--}}

<x-guest-layout>
    {{-- Definimos el encabezado dinámico para esta página --}}
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-slate-800">Crear Cuenta</h1>
        <p class="text-slate-500 mt-1 text-sm">Registre al nuevo personal de la IPS.</p>
    </x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nombre Completo --}}
        <div>
            <x-input-label for="name" :value="__('Nombre Completo')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus placeholder="Nombre y Apellidos" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Correo Electrónico --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo Institucional')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="ejemplo@ipscrear.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Selector de Especialidad / Rol --}}
        <div class="mt-4">
            <x-input-label for="role_id" :value="__('Especialidad / Rol Médico')" />
            <select name="role_id" id="role_id" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm py-2" required>
                <option value="" disabled selected>Seleccione una opción...</option>
                {{-- Bucle para cargar roles desde la base de datos --}}
                @foreach(\App\Models\Role::all() as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>

        {{-- Sección de Contraseñas (Dos columnas en pantallas grandes) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="password" :value="__('Contraseña')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            </div>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />

        {{-- Botones finales --}}
        <div class="flex items-center justify-between mt-8">
            <a class="text-sm text-gray-600 underline hover:text-blue-700 transition-colors" href="{{ route('login') }}">
                ¿Ya tienes una cuenta?
            </a>

            <x-primary-button class="bg-blue-600 hover:bg-blue-700 px-8">
                {{ __('REGISTRAR') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>