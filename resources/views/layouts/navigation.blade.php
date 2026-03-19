<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-blue-600" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Inicio') }}
                    </x-nav-link>

                    {{-- Módulo de Admisión: Solo visible para Admisión y Administradores --}}
                    {{-- Usamos Str::lower para que no importe si está en mayúsculas o minúsculas --}}
                    @if(in_array(Str::lower(Auth::user()->role->name), ['admisión', 'admision', 'administrador']))
                        <x-nav-link :href="route('students.index')" :active="request()->routeIs('students.*')">
                            {{ __('Registrar Estudiante') }}
                        </x-nav-link>
                    @endif

                    {{-- Módulo Médico: Visible para cualquier rol que NO sea Admisión --}}
                    @if(!in_array(Str::lower(Auth::user()->role->name), ['admisión', 'admision']))
                        <x-nav-link :href="route('medical_exams.index')" :active="request()->routeIs('medical_exams.*')">
                            {{ __('Bandeja de Pacientes') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- Badge de Rol Dinámico con Colores por Especialidad --}}
                @php
                    $roleName = Str::lower(Auth::user()->role->name);
                    $colorClass = match($roleName) {
                        'administrador' => 'bg-purple-100 text-purple-800 border-purple-200',
                        'admisión', 'admision' => 'bg-green-100 text-green-800 border-green-200',
                        'odontología', 'odontologia' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'optometría', 'optometria' => 'bg-amber-100 text-amber-800 border-amber-200',
                        default => 'bg-gray-100 text-gray-800 border-gray-200',
                    };
                @endphp

                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold {{ $colorClass }} mr-3 uppercase tracking-widest border shadow-sm">
                    {{ Auth::user()->role->name }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="font-bold">{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Mi Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- ... (Resto del código del menú móvil igual) ... --}}
        </div>
    </div>
</nav>
