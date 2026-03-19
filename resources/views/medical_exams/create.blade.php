<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Circuito Médico Activo') }}
            </h2>
            <span class="px-4 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold shadow-sm border border-blue-200">
                Estudiante: {{ $student->full_name }}
            </span>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'general' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 sticky top-6">
                        <div class="text-center mb-4">
                            <div class="h-20 w-20 bg-blue-500 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-2 shadow-inner">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </div>
                            <h3 class="font-bold text-gray-900 leading-tight">{{ $student->full_name }}</h3>
                            <p class="text-xs text-gray-500 uppercase tracking-tighter">{{ $student->document_type }}: {{ $student->document_number }}</p>
                        </div>
                        <hr class="my-4">
                        <div class="space-y-3">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Grado</p>
                                <p class="text-sm font-medium text-gray-700">{{ $student->grade ?? 'No registrado' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Estado del Circuito</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $exam->status === 'en_proceso' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $exam->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">

                        {{-- Navegación de Pestañas --}}
                        <div class="border-b border-gray-200 bg-gray-50/50">
                            <nav class="flex -mb-px px-6 space-x-8">
                                <button @click="tab = 'general'" :class="tab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-bold text-sm transition focus:outline-none">
                                    Medicina General
                                </button>
                                <button @click="tab = 'optometria'" :class="tab === 'optometria' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-bold text-sm transition focus:outline-none">
                                    Optometría
                                </button>
                                <button @click="tab = 'odontologia'" :class="tab === 'odontologia' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="py-4 px-1 border-b-2 font-bold text-sm transition focus:outline-none">
                                    Odontología
                                </button>
                            </nav>
                        </div>

                        <div class="p-8">
                            <form action="{{ route('medical_exams.store_result', $exam) }}" method="POST">
                                @csrf

                                {{-- Pestaña: Medicina General --}}
                                <div x-show="tab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <x-input-label for="peso" value="Peso (kg)" />
                                            <x-text-input name="results[peso]" type="number" step="0.1" class="mt-1 block w-full" placeholder="Ej: 45.5" />
                                        </div>
                                        <div>
                                            <x-input-label for="talla" value="Talla (cm)" />
                                            <x-text-input name="results[talla]" type="number" class="mt-1 block w-full" placeholder="Ej: 150" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <x-input-label for="obs" value="Hallazgos y Observaciones" />
                                            <textarea name="notes" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Escriba aquí los hallazgos médicos..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pestaña: Optometría --}}
                                <div x-show="tab === 'optometria'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 md:col-span-2">
                                            <h4 class="text-blue-800 font-bold text-sm">Valoración de Agudeza Visual</h4>
                                        </div>
                                        <div>
                                            <x-input-label value="Ojo Derecho" />
                                            <x-text-input name="results[ojo_derecho]" type="text" class="mt-1 block w-full" placeholder="Ej: 20/20" />
                                        </div>
                                        <div>
                                            <x-input-label value="Ojo Izquierdo" />
                                            <x-text-input name="results[ojo_izquierdo]" type="text" class="mt-1 block w-full" placeholder="Ej: 20/20" />
                                        </div>
                                    </div>
                                </div>

                                {{-- Pestaña: Odontología --}}
                                <div x-show="tab === 'odontologia'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95">
                                    <div class="space-y-4">
                                        <x-input-label value="Estado Clínico Dental" />
                                        <textarea name="results[estado_dental]" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Caries evidentes, estado de encías..."></textarea>
                                    </div>
                                </div>

                                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
                                    <p class="text-xs text-gray-400 italic">Los datos se guardarán como borrador hasta finalizar el circuito.</p>
                                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 shadow-md">
                                        {{ __('Guardar Registro de esta Área') }}
                                    </x-primary-button>
                                </div>
                            </form>

                            <hr class="my-10 border-dashed">

                            {{-- Botón de Cierre de Circuito --}}
                            <div class="bg-green-50 rounded-xl p-8 border border-green-100 text-center">
                                <h4 class="text-green-800 font-bold mb-2">¿Todas las áreas han sido evaluadas?</h4>
                                <p class="text-green-600 text-sm mb-6">Al finalizar, se generará el documento oficial para el departamento de admisiones.</p>
                                <form action="{{ route('medical_exams.finish', $exam) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-10 py-4 bg-green-600 hover:bg-green-700 text-white font-black rounded-xl shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        FINALIZAR Y ARCHIVAR CIRCUITO
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
