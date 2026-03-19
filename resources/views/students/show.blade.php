<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ficha Médica Escolar: ') }} {{ $student->first_name }} {{ $student->last_name }}
            </h2>
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-md text-xs uppercase tracking-widest hover:bg-gray-700 transition">
                Imprimir Ficha
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">

                {{-- Encabezado Estilo Reporte --}}
                <div class="p-8 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-blue-700">CLÍNICA IPS - MÓDULO ESCOLAR</h1>
                        <p class="text-sm text-gray-500 mt-1">Sistema de Gestión de Salud Estudiantil</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Desarrollado por</span>
                        <p class="text-sm font-black text-blue-600">SnakeDev</p>
                    </div>
                </div>

                <div class="p-8">
                    {{-- SECCIÓN 1: DATOS DEL ESTUDIANTE --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-4 tracking-widest">Datos Personales</h4>
                            <div class="space-y-3">
                                <p class="text-sm"><span class="font-bold text-gray-600">Nombre Completo:</span> {{ $student->first_name }} {{ $student->last_name }}</p>
                                <p class="text-sm"><span class="font-bold text-gray-600">Identificación:</span> {{ $student->document_type }} - {{ $student->document_number }}</p>
                                <p class="text-sm"><span class="font-bold text-gray-600">Edad:</span> {{ $student->age }} años</p>
                                <p class="text-sm"><span class="font-bold text-gray-600">Género:</span> {{ $student->gender }}</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-4 tracking-widest">Ubicación Académica</h4>
                            <div class="space-y-3">
                                <p class="text-sm"><span class="font-bold text-gray-600">Grado Actual:</span>
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">{{ $student->grade }}</span>
                                </p>
                                <p class="text-sm"><span class="font-bold text-gray-600">Fecha de Matrícula:</span> {{ $student->created_at->format('d/m/Y') }}</p>
                                <p class="text-sm"><span class="font-bold text-gray-600">Colegio Anterior:</span> {{ $student->previous_school }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: DATOS DEL ACUDIENTE (Agregada) --}}
                    <div class="bg-blue-50 p-6 rounded-lg mb-10 border border-blue-100">
                        <h4 class="text-xs font-bold text-blue-800 uppercase mb-4 tracking-widest">Información del Acudiente</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-xs text-blue-600 font-bold uppercase">Nombre</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $student->guardian_name }} {{ $student->guardian_lastname }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-blue-600 font-bold uppercase">Teléfono</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $student->guardian_phone }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-blue-600 font-bold uppercase">Parentesco</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $student->guardian_relationship }}</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-blue-100 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-blue-600 font-bold uppercase">Documento</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $student->guardian_document }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-blue-600 font-bold uppercase">Correo Electrónico</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $student->guardian_email }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Sección de Exámenes (Historial) --}}
                    <div class="mt-10">
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-4 tracking-widest">Historial de Circuito Médico</h4>
                        @if($student->medicalExams && $student->medicalExams->count() > 0)
                            <div class="border rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Fecha</th>
                                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Tipo de Examen</th>
                                            <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Resultado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($student->medicalExams as $exam)
                                            <tr>
                                                <td class="px-4 py-3 text-sm">{{ $exam->created_at->format('d/m/Y') }}</td>
                                                <td class="px-4 py-3 text-sm font-semibold">{{ $exam->type }}</td>
                                                <td class="px-4 py-3 text-sm italic text-gray-600">{{ $exam->result }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4">
                                <p class="text-sm text-amber-700">No hay exámenes registrados.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Acciones Finales --}}
                    <div class="mt-12 flex justify-between items-center border-t pt-6 no-print">
                        <a href="{{ route('students.index') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">
                            &larr; Volver al listado
                        </a>
                        <a href="{{ route('students.edit', $student) }}" class="bg-amber-500 text-white px-4 py-2 rounded-md text-xs font-bold uppercase hover:bg-amber-600 transition shadow-sm">
                            Editar Datos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            .py-12 { padding: 0 !important; }
            .shadow-sm { box-shadow: none !important; }
            .border { border: 1px solid #e5e7eb !important; }
            body { background: white !important; }
        }
    </style>
</x-app-layout>
