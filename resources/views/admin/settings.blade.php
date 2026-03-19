<x-app-layout>
    <x-slot name="header">Configuración Snake_DEV</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('status'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 shadow-sm mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-green-800 uppercase tracking-tighter">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-xl border border-slate-200 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Información del Usuario</th>
                            <th class="px-6 py-4">Rol Asignado</th>
                            <th class="px-6 py-4">Color de Identidad</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                        <tr>
                            <form action="{{ route('admin.user.update', $user) }}" method="POST" id="form-{{ $user->id }}">
                                @csrf @method('PATCH')
                                
                                <td class="px-6 py-4">
                                    <input type="text" name="name" value="{{ $user->name }}" 
                                        class="text-xs font-bold text-slate-800 border-none focus:ring-0 p-0 bg-transparent w-full" placeholder="Nombre">
                                    <input type="text" name="job_title" value="{{ $user->job_title }}" 
                                        class="text-[10px] text-slate-400 border-none focus:ring-0 p-0 bg-transparent w-full uppercase" placeholder="SIN CARGO DEFINIDO">
                                </td>

                                <td class="px-6 py-4">
                                    <select name="role_id" class="text-xs border-slate-200 rounded-lg focus:ring-blue-500 py-1">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <input type="color" name="ui_color" value="{{ $user->ui_color ?? '#3b82f6' }}" 
                                            class="h-6 w-6 rounded border-none p-0 cursor-pointer shadow-sm">
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $user->ui_color ?? '#3B82F6' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center space-x-3">
                                    <button type="submit" class="text-blue-500 hover:text-blue-700 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                    </button>
                            </form>

                                    <form action="{{ route('admin.users.revoke', $user) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition" 
                                            onclick="return confirm('¿Estás seguro de quitar los permisos a {{ $user->name }}?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </button>
                                    </form>
                                </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 shadow sm:rounded-xl border border-slate-200">
                    <h3 class="text-xs font-bold text-slate-800 uppercase mb-2">Seguridad de Cuentas</h3>
                    <p class="text-[10px] text-slate-400 mb-4 uppercase">Cierra todas las sesiones activas en otros dispositivos.</p>
                    <form action="{{ route('admin.reset_access') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-slate-800 text-white py-3 rounded-lg font-bold text-[10px] uppercase tracking-widest hover:bg-black transition shadow-sm">
                            Restablecer Accesos
                        </button>
                    </form>
                </div>

                <div class="bg-white p-6 shadow sm:rounded-xl border border-slate-200">
                    <h3 class="text-xs font-bold text-slate-800 uppercase mb-2">Personalización Visual</h3>
                    <p class="text-[10px] text-slate-400 mb-4 uppercase">Define los colores globales para cada rol médico.</p>
                    <a href="{{ route('admin.role_colors') }}" class="block w-full text-center border-2 border-blue-600 text-blue-600 py-3 rounded-lg font-bold text-[10px] uppercase tracking-widest hover:bg-blue-50 transition">
                        Editar Colores de Rol
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>