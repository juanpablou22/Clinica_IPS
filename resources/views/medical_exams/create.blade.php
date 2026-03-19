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

    {{-- Lógica para detectar pestaña basada en el área del usuario --}}
    <div class="py-12" x-data="{ tab: '{{ $userArea }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Sidebar de Información --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 sticky top-6">
                        <div class="text-center mb-4">
                            <div class="h-20 w-20 bg-blue-500 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-2 shadow-inner">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </div>
                            <h3 class="font-bold text-gray-900 leading-tight">{{ $student->full_name }}</h3>
                            <p class="text-xs text-gray-500 uppercase tracking-tighter">{{ $student->document_type }}: {{ $student->document_number }}</p>
                        </div>
                        <hr class="my-4 border-gray-100">
                        <div class="space-y-3">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Estado del Circuito</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $medicalExam->status === 'en_proceso' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $medicalExam->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Área Principal --}}
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">

                        <div class="border-b border-gray-200 bg-gray-50/50">
                            <nav class="flex -mb-px px-6 space-x-8">
                                <button @click="tab = '{{ $userArea }}'" :class="tab === '{{ $userArea }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'" class="py-4 px-1 border-b-2 font-bold text-sm transition focus:outline-none">{{ ucfirst(str_replace('_', ' ', $userArea)) }}</button>
                            </nav>
                        </div>

                        <div class="p-8">
                            <form action="{{ route('medical_exams.store_result', $medicalExam) }}" method="POST">
                                @csrf

                                @if($userArea == 'odontologia')
                                    {{-- Tab: Odontología --}}
                                    <div x-show="tab === 'odontologia'" x-cloak>
                                        <div class="bg-blue-100 p-6 rounded-2xl text-gray-900">
                                            <h4 class="text-blue-400 font-bold mb-4 text-xs uppercase text-center">Carta Dental Completa</h4>
                                            <!-- Paleta descriptiva de colores -->
                                            <div class="flex justify-center gap-4 mb-6">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-7 h-7 rounded-full bg-white border-2 border-gray-400 mb-1"></div>
                                                    <span class="text-[10px] text-black">Normal</span>
                                                </div>
                                                <div class="flex flex-col items-center">
                                                    <div class="w-7 h-7 rounded-full bg-green-500 mb-1"></div>
                                                    <span class="text-[10px] text-black">Sellante</span>
                                                </div>
                                                <div class="flex flex-col items-center">
                                                    <div class="w-7 h-7 rounded-full bg-red-600 mb-1"></div>
                                                    <span class="text-[10px] text-black">Caries</span>
                                                </div>
                                                <div class="flex flex-col items-center">
                                                    <div class="w-7 h-7 rounded-full bg-blue-600 mb-1"></div>
                                                    <span class="text-[10px] text-black">Obturación</span>
                                                </div>
                                                <div class="flex flex-col items-center">
                                                    <div class="w-7 h-7 rounded-full bg-gray-700 mb-1"></div>
                                                    <span class="text-[10px] text-black">Ausente</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-center space-y-2">
                                                {{-- Fila superior permanente --}}
                                                <div class="flex justify-center gap-2 mb-2">
                                                    @foreach([18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28] as $n)
                                                        <div x-data="{ status: 'white' }"
                                                             @click="status = (status === 'white' ? 'green' : (status === 'green' ? 'red' : (status === 'red' ? 'blue' : (status === 'blue' ? 'gray' : 'white'))))"
                                                             :class="{'bg-white border-gray-400 text-gray-800': status === 'white', 'bg-green-500 text-white': status === 'green', 'bg-red-600 text-white animate-pulse': status === 'red', 'bg-blue-600 text-white': status === 'blue', 'bg-gray-700 text-white': status === 'gray'}"
                                                             class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center cursor-pointer transition-all">
                                                            <span class="text-[9px] font-bold">{{ $n }}</span>
                                                            <input type="hidden" name="results[odontograma][{{ $n }}]" :value="status">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                {{-- Fila superior temporal --}}
                                                <div class="flex justify-center gap-2 mb-2">
                                                    @foreach([55,54,53,52,51,61,62,63,64,65] as $n)
                                                        <div x-data="{ status: 'white' }"
                                                             @click="status = (status === 'white' ? 'green' : (status === 'green' ? 'red' : (status === 'red' ? 'blue' : (status === 'blue' ? 'gray' : 'white'))))"
                                                             :class="{'bg-white border-gray-400 text-gray-800': status === 'white', 'bg-green-500 text-white': status === 'green', 'bg-red-600 text-white animate-pulse': status === 'red', 'bg-blue-600 text-white': status === 'blue', 'bg-gray-700 text-white': status === 'gray'}"
                                                             class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center cursor-pointer transition-all">
                                                            <span class="text-[9px] font-bold">{{ $n }}</span>
                                                            <input type="hidden" name="results[odontograma][{{ $n }}]" :value="status">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                {{-- Fila inferior temporal --}}
                                                <div class="flex justify-center gap-2 mb-2">
                                                    @foreach([85,84,83,82,81,71,72,73,74,75] as $n)
                                                        <div x-data="{ status: 'white' }"
                                                             @click="status = (status === 'white' ? 'green' : (status === 'green' ? 'red' : (status === 'red' ? 'blue' : (status === 'blue' ? 'gray' : 'white'))))"
                                                             :class="{'bg-white border-gray-400 text-gray-800': status === 'white', 'bg-green-500 text-white': status === 'green', 'bg-red-600 text-white animate-pulse': status === 'red', 'bg-blue-600 text-white': status === 'blue', 'bg-gray-700 text-white': status === 'gray'}"
                                                             class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center cursor-pointer transition-all">
                                                            <span class="text-[9px] font-bold">{{ $n }}</span>
                                                            <input type="hidden" name="results[odontograma][{{ $n }}]" :value="status">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                {{-- Fila inferior permanente --}}
                                                <div class="flex justify-center gap-2 mb-2">
                                                    @foreach([48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38] as $n)
                                                        <div x-data="{ status: 'white' }"
                                                             @click="status = (status === 'white' ? 'green' : (status === 'green' ? 'red' : (status === 'red' ? 'blue' : (status === 'blue' ? 'gray' : 'white'))))"
                                                             :class="{'bg-white border-gray-400 text-gray-800': status === 'white', 'bg-green-500 text-white': status === 'green', 'bg-red-600 text-white animate-pulse': status === 'red', 'bg-blue-600 text-white': status === 'blue', 'bg-gray-700 text-white': status === 'gray'}"
                                                             class="w-8 h-8 rounded-full border-2 border-gray-400 flex items-center justify-center cursor-pointer transition-all">
                                                            <span class="text-[9px] font-bold">{{ $n }}</span>
                                                            <input type="hidden" name="results[odontograma][{{ $n }}]" :value="status">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!-- Preguntas de Salud Oral -->
                                            <div class="mt-8 bg-blue-50 p-4 rounded-lg">
                                                <h5 class="text-white text-base font-semibold mb-4 flex items-center gap-2">
                                                    <span>🦷</span>
                                                    <span>Salud oral</span>
                                                </h5>
                                                <ol class="text-black text-sm space-y-3 pl-4">
                                                    <li>
                                                        <span class="font-medium">1. ¿El niño se cepilla los dientes diariamente?</span><br>
                                                        <label class="inline-flex items-center mr-4">
                                                            <input type="radio" name="results[salud_oral][cepilla]" value="si" class="form-radio text-blue-500"> <span class="ml-1">Sí</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="results[salud_oral][cepilla]" value="no" class="form-radio text-blue-500"> <span class="ml-1">No</span>
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <span class="font-medium">2. ¿Ha tenido caries?</span><br>
                                                        <label class="inline-flex items-center mr-4">
                                                            <input type="radio" name="results[salud_oral][caries]" value="no" class="form-radio text-blue-500"> <span class="ml-1">No</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="results[salud_oral][caries]" value="si" class="form-radio text-blue-500"> <span class="ml-1">Sí</span>
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <span class="font-medium">3. ¿Ha recibido tratamiento odontológico?</span><br>
                                                        <label class="inline-flex items-center mr-4">
                                                            <input type="radio" name="results[salud_oral][tratamiento]" value="no" class="form-radio text-blue-500"> <span class="ml-1">No</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="results[salud_oral][tratamiento]" value="si" class="form-radio text-blue-500"> <span class="ml-1">Sí</span>
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <span class="font-medium">4. ¿Ha visitado al odontólogo en el último año?</span><br>
                                                        <label class="inline-flex items-center mr-4">
                                                            <input type="radio" name="results[salud_oral][visita_anual]" value="si" class="form-radio text-blue-500"> <span class="ml-1">Sí</span>
                                                        </label>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="results[salud_oral][visita_anual]" value="no" class="form-radio text-blue-500"> <span class="ml-1">No</span>
                                                        </label>
                                                    </li>
                                                </ol>
                                            </div>
                                            <textarea name="results[estado_dental]" class="mt-6 w-full bg-blue-50 border-none rounded-lg text-sm text-gray-900" placeholder="Diagnóstico dental..."></textarea>
                                        </div>
                                    </div>
                                @elseif($userArea == 'optometria')
                                    {{-- Tab: Optometría --}}
                                    <div x-show="tab === 'optometria'" x-cloak>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <x-input-label value="Ojo Derecho" />
                                                <x-text-input name="results[ojo_derecho]" type="text" class="mt-1 block w-full" placeholder="20/20" />
                                            </div>
                                            <div>
                                                <x-input-label value="Ojo Izquierdo" />
                                                <x-text-input name="results[ojo_izquierdo]" type="text" class="mt-1 block w-full" placeholder="20/20" />
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Tab: Medicina General --}}
                                    <div x-show="tab === 'general'" x-cloak>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <x-input-label value="Peso (kg)" />
                                                <x-text-input name="results[peso]" type="number" step="0.1" class="mt-1 block w-full" />
                                            </div>
                                            <div>
                                                <x-input-label value="Talla (cm)" />
                                                <x-text-input name="results[talla]" type="number" class="mt-1 block w-full" />
                                            </div>
                                            <div class="md:col-span-2">
                                                <x-input-label value="Observaciones" />
                                                <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-8 pt-6 border-t flex justify-end">
                                    <x-primary-button class="bg-blue-600">{{ __('Guardar Resultados') }}</x-primary-button>
                                </div>
                            </form>

                            {{-- Cierre de Circuito --}}
                            <div class="mt-10 bg-green-50 p-6 rounded-xl border border-green-100 text-center">
                                <form action="{{ route('medical_exams.finish', $medicalExam) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg font-bold">FINALIZAR CIRCUITO</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
