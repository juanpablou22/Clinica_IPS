<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight capitalize">
                {{ __('Panel de Evaluación: ') }} <span class="text-blue-600">{{ $userArea }}</span>
            </h2>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full uppercase tracking-wider border border-blue-200">
                    {{ Auth::user()->role->name }}
                </span>
                <span class="px-3 py-1 bg-gray-800 text-white text-xs font-bold rounded-full">
                    {{ $pendingExams->count() }} Pacientes
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-sm rounded-r-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-700">Estudiantes Pendientes de Valoración</h3>
                        <div class="text-sm text-gray-500 italic">
                            * Solo se muestran estudiantes con circuito médico activo para su área.
                        </div>
                    </div>

                    @if($pendingExams->isEmpty())
                        <div class="bg-gray-50 border border-dashed border-gray-300 text-gray-500 p-12 text-center rounded-xl">
                            <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xl font-medium">¡Bandeja vacía!</p>
                            <p class="text-gray-400">No hay pacientes pendientes para {{ $userArea }} en este momento.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estudiante</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Documento</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Grado Escolar</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado Circuito</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha Orden</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingExams as $exam)
                                        <tr class="hover:bg-blue-50/50 transition duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3">
                                                        {{ substr($exam->student->first_name, 0, 1) }}
                                                    </div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $exam->student->full_name }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                <span class="font-mono bg-gray-100 px-2 py-1 rounded text-xs">{{ $exam->student->document_type }}: {{ $exam->student->document_number }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-medium rounded border border-indigo-100">
                                                    {{ $exam->student->grade ?? 'No asignado' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($exam->status === 'en_proceso')
                                                    <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full animate-pulse">
                                                        ● En Proceso
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full">
                                                        Pendiente Inicio
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                                {{ $exam->created_at->diffForHumans() }}
                                                <br>
                                                <span class="text-[10px]">{{ $exam->created_at->format('d/m/Y h:i A') }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                {{-- BOTÓN ACTUALIZADO CON PARÁMETRO DE SECCIÓN --}}
                                                @php
                                                    // Detectamos la sección según el área del usuario logueado
                                                    $targetSection = match(strtolower($userArea)) {
                                                        'odontologia' => 'odontologia',
                                                        'optometria'  => 'optometria',
                                                        default       => 'general',
                                                    };
                                                @endphp

                                                <a href="{{ route('medical_exams.evaluate', $exam) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-md hover:shadow-lg">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Evaluar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
