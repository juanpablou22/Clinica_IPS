@php
    // 1. LÓGICA DINÁMICA: Extraemos datos del usuario autenticado
    $user = Auth::user();
    
    // Normalizamos el nombre del rol (manejamos tildes y mayúsculas)
    $roleName = Str::lower($user->role->name ?? 'invitado');
    
    // Color de identidad visual desde la BD o azul por defecto
    $userColor = $user->ui_color ?? '#3b82f6'; 
    
    // Definición de estilos por Rol para los Badges
    $colorClass = match($roleName) {
        'administrador' => 'bg-purple-100 text-purple-800 border-purple-200',
        'admisión', 'admision' => 'bg-green-100 text-green-800 border-green-200',
        'odontología', 'odontologia' => 'bg-blue-100 text-blue-800 border-blue-200',
        'optometría', 'optometria' => 'bg-amber-100 text-amber-800 border-amber-200',
        default => 'bg-gray-100 text-gray-800 border-gray-200',
    };
@endphp

<div class="flex flex-col h-full bg-white border-r border-slate-100">
    
    <div class="px-4 pt-6 mb-4">
        <div x-show="sidebarOpen" class="relative group">
            <form action="{{ route('medical_exams.index') }}" method="GET">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Buscar paciente..." 
                       class="w-full py-2 pl-10 pr-4 text-xs font-medium bg-slate-50 border border-slate-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition-all placeholder:text-slate-400 text-slate-700">
            </form>
        </div>
        <div x-show="!sidebarOpen" class="flex justify-center">
            <button class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>
    </div>

    <nav class="flex-1 px-3 space-y-2 overflow-y-auto">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center p-3 rounded-xl transition group {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span x-show="sidebarOpen" class="ml-3 font-medium text-sm truncate">Inicio</span>
        </a>

        @if(in_array($roleName, ['admisión', 'admision', 'administrador']))
            <a href="{{ route('students.index') }}" 
               class="flex items-center p-3 rounded-xl transition group {{ request()->routeIs('students.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm truncate">Registrar Estudiante</span>
            </a>
        @endif

        @if(!in_array($roleName, ['admisión', 'admision']))
            <a href="{{ route('medical_exams.index') }}" 
               class="flex items-center p-3 rounded-xl transition group {{ request()->routeIs('medical_exams.index') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm truncate">Bandeja de Pacientes</span>
            </a>

            <a href="{{ route('medical_exams.history') }}" 
               class="flex items-center p-3 rounded-xl transition group {{ request()->routeIs('medical_exams.history') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm truncate">Historial de Pacientes</span>
            </a>
        @endif
    </nav>

    <div class="p-4 border-t border-slate-100 bg-slate-50 flex-shrink-0">
        <div class="flex items-center transition-all duration-300" :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <div class="h-12 w-12 rounded-full flex items-center justify-center text-white font-bold border-2 border-white shadow-md flex-shrink-0 overflow-hidden relative group"
                 style="background-color: {{ $userColor }};">
                
                @if($user->profile_photo_path)
                    <img src="{{ $user->profile_photo_url }}" class="h-full w-full object-cover">
                @else
                    {{ substr($user->name, 0, 1) }}
                @endif
            </div>
            
            <div x-show="sidebarOpen" class="ml-3 overflow-hidden">
                <p class="text-xs font-bold text-slate-800 truncate">{{ $user->name }}</p>
                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $colorClass }} border mt-1 uppercase tracking-tighter">
                    {{ $user->role->name ?? 'Sin Rol' }}
                </span>
            </div>
        </div>

        <div class="mt-4 space-y-1">
            <a href="{{ route('profile.edit') }}" class="flex items-center p-2 rounded-lg text-slate-600 hover:bg-white hover:shadow-sm transition group">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span x-show="sidebarOpen" class="ml-3 text-xs font-medium">Mi Cuenta</span>
            </a>

            @if($roleName === 'administrador')
                <a href="{{ route('admin.settings') }}" 
                   class="flex items-center p-2 rounded-lg text-blue-600 bg-blue-50/50 hover:bg-white hover:shadow-sm transition group border border-blue-100">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-xs font-bold uppercase tracking-tighter">Panel Admin</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center p-2 rounded-lg text-red-500 hover:bg-red-50 transition group text-left">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 text-xs font-bold uppercase">Salir</span>
                </button>
            </form>
        </div>
    </div>
</div>