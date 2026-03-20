<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Valoracion Médica Activa - Odontología') }}
            </h2>
            <span class="px-4 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold shadow-sm border border-blue-200">
                Estudiante: {{ $student->full_name }}
            </span>
        </div>
    </x-slot>

    {{-- Librería para convertir HTML a Imagen --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <div class="py-12" x-data="{ tab: '{{ $userArea }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- Sidebar de Información del Estudiante --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 sticky top-6">
                        <div class="text-center mb-4">
                            <div class="h-20 w-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-2 shadow-inner">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </div>
                            <h3 class="font-bold text-gray-900 leading-tight">{{ $student->full_name }}</h3>
                            <p class="text-xs text-gray-500 uppercase tracking-tighter">{{ $student->document_type }}: {{ $student->document_number }}</p>
                        </div>
                        <hr class="my-4 border-gray-100">
                        <div class="space-y-3 text-center">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estado del Proceso</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $medicalExam->status === 'en_proceso' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                {{ ucfirst(str_replace('_', ' ', $medicalExam->status)) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Panel Principal --}}
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-8">
                            <form id="form-odontologia" action="{{ route('medical_exams.store_result', $medicalExam) }}" method="POST">
                                @csrf

                                {{-- Campo oculto para la imagen del odontograma --}}
                                <input type="hidden" name="odontograma_imagen" id="odontograma_imagen">

                                <div class="space-y-10">

                                    {{-- 1. ODONTOGRAMA INTERACTIVO --}}
                                    <div id="capture-area" class="bg-slate-50 p-8 rounded-3xl border border-slate-200 shadow-inner">
                                        <h4 class="text-slate-500 font-black mb-10 text-xs uppercase text-center tracking-[0.2em]">Odontograma Interactivo (Dentición Mixta)</h4>

                                        <div class="flex flex-wrap justify-center gap-6 mb-12 border-b border-slate-200 pb-8" data-html2canvas-ignore>
                                            @foreach(['white' => 'Sano', 'red' => 'Caries', 'blue' => 'Obturado', 'green' => 'Sellante', 'gray' => 'Ausente'] as $color => $label)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-5 h-5 rounded border border-black {{ $color === 'white' ? 'bg-white' : ($color === 'red' ? 'bg-red-600' : ($color === 'blue' ? 'bg-blue-600' : ($color === 'green' ? 'bg-green-500' : 'bg-gray-800'))) }}"></div>
                                                    <span class="text-[11px] font-bold text-slate-600 uppercase">{{ $label }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <script>
                                            function toothLogic() {
                                                return {
                                                    faces: { top: 'white', bottom: 'white', left: 'white', right: 'white', center: 'white' },
                                                    toggle(face) {
                                                        const colors = ['white', 'red', 'blue', 'green', 'gray'];
                                                        this.faces[face] = colors[(colors.indexOf(this.faces[face]) + 1) % colors.length];
                                                    },
                                                    getColor(face) {
                                                        return { 'white':'#ffffff', 'red':'#dc2626', 'blue':'#2563eb', 'green':'#16a34a', 'gray':'#1f2937' }[this.faces[face]];
                                                    }
                                                }
                                            }
                                        </script>

                                        <div class="flex flex-col items-center space-y-12">
                                            {{-- Permanentes Superiores --}}
                                            <div class="flex flex-wrap justify-center gap-1">
                                                @foreach([18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28] as $n)
                                                    <div x-data="toothLogic()" class="flex flex-col items-center">
                                                        <svg width="34" height="34" viewBox="0 0 100 100" class="cursor-pointer">
                                                            <path @click="toggle('top')" :fill="getColor('top')" d="M0,0 L100,0 L70,30 L30,30 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('right')" :fill="getColor('right')" d="M100,0 L100,100 L70,70 L70,30 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('bottom')" :fill="getColor('bottom')" d="M0,100 L100,100 L70,70 L30,70 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('left')" :fill="getColor('left')" d="M0,0 L0,100 L30,70 L30,30 Z" stroke="#000000" stroke-width="2" />
                                                            <rect @click="toggle('center')" :fill="getColor('center')" x="30" y="30" width="40" height="40" stroke="#000000" stroke-width="2" />
                                                        </svg>
                                                        <span class="text-[10px] font-bold mt-1 text-slate-800">{{ $n }}</span>
                                                        <template x-for="(val, face) in faces">
                                                            <input type="hidden" :name="`results[${face}][{{ $n }}]`" :value="val">
                                                        </template>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Temporales Superiores --}}
                                            <div class="flex flex-wrap justify-center gap-1">
                                                @foreach([55,54,53,52,51, 61,62,63,64,65] as $n)
                                                    <div x-data="toothLogic()" class="flex flex-col items-center">
                                                        <svg width="34" height="34" viewBox="0 0 100 100" class="cursor-pointer">
                                                            <path @click="toggle('top')" :fill="getColor('top')" d="M0,0 L100,0 L70,30 L30,30 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('right')" :fill="getColor('right')" d="M100,0 L100,100 L70,70 L70,30 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('bottom')" :fill="getColor('bottom')" d="M0,100 L100,100 L70,70 L30,70 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('left')" :fill="getColor('left')" d="M0,0 L0,100 L30,70 L30,30 Z" stroke="#000000" stroke-width="2" />
                                                            <rect @click="toggle('center')" :fill="getColor('center')" x="30" y="30" width="40" height="40" stroke="#000000" stroke-width="2" />
                                                        </svg>
                                                        <span class="text-[10px] font-bold mt-1 text-slate-800">{{ $n }}</span>
                                                        <template x-for="(val, face) in faces">
                                                            <input type="hidden" :name="`results[${face}][{{ $n }}]`" :value="val">
                                                        </template>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="w-full border-t-2 border-slate-200 border-dashed"></div>

                                            {{-- Temporales Inferiores --}}
                                            <div class="flex flex-wrap justify-center gap-1">
                                                @foreach([85,84,83,82,81, 71,72,73,74,75] as $n)
                                                    <div x-data="toothLogic()" class="flex flex-col items-center">
                                                        <svg width="34" height="34" viewBox="0 0 100 100" class="cursor-pointer">
                                                            <path @click="toggle('top')" :fill="getColor('top')" d="M0,0 L100,0 L70,30 L30,30 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('right')" :fill="getColor('right')" d="M100,0 L100,100 L70,70 L70,30 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('bottom')" :fill="getColor('bottom')" d="M0,100 L100,100 L70,70 L30,70 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('left')" :fill="getColor('left')" d="M0,0 L0,100 L30,70 L30,30 Z" stroke="#000000" stroke-width="2" />
                                                            <rect @click="toggle('center')" :fill="getColor('center')" x="30" y="30" width="40" height="40" stroke="#000000" stroke-width="2" />
                                                        </svg>
                                                        <span class="text-[10px] font-bold mt-1 text-slate-800">{{ $n }}</span>
                                                        <template x-for="(val, face) in faces">
                                                            <input type="hidden" :name="`results[${face}][{{ $n }}]`" :value="val">
                                                        </template>
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Permanentes Inferiores --}}
                                            <div class="flex flex-wrap justify-center gap-1">
                                                @foreach([48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38] as $n)
                                                    <div x-data="toothLogic()" class="flex flex-col items-center">
                                                        <svg width="34" height="34" viewBox="0 0 100 100" class="cursor-pointer">
                                                            <path @click="toggle('top')" :fill="getColor('top')" d="M0,0 L100,0 L70,30 L30,30 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('right')" :fill="getColor('right')" d="M100,0 L100,100 L70,70 L70,30 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('bottom')" :fill="getColor('bottom')" d="M0,100 L100,100 L70,70 L30,70 Z" stroke="#000000" stroke-width="2" />
                                                            <path @click="toggle('left')" :fill="getColor('left')" d="M0,0 L0,100 L30,70 L30,30 Z" stroke="#000000" stroke-width="2" />
                                                            <rect @click="toggle('center')" :fill="getColor('center')" x="30" y="30" width="40" height="40" stroke="#000000" stroke-width="2" />
                                                        </svg>
                                                        <span class="text-[10px] font-bold mt-1 text-slate-800">{{ $n }}</span>
                                                        <template x-for="(val, face) in faces">
                                                            <input type="hidden" :name="`results[${face}][{{ $n }}]`" :value="val">
                                                        </template>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 2. SECCIÓN DE ANTECEDENTES Y HÁBITOS --}}
                                    <div class="bg-blue-50/40 p-6 rounded-xl border border-blue-100 shadow-sm">
                                        <h4 class="text-blue-700 font-bold mb-6 flex items-center gap-2 uppercase text-xs tracking-[0.1em]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Antecedentes y Hábitos
                                        </h4>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @php
                                                $preguntas = [
                                                    'p1' => '1. ¿Se cepilla los dientes diariamente?',
                                                    'p2' => '2. ¿Ha tenido caries anteriormente?',
                                                    'p3' => '3. ¿Ha recibido tratamiento dental?',
                                                    'p4' => '4. ¿Visitó al odontólogo el último año?'
                                                ];
                                            @endphp

                                            @foreach($preguntas as $key => $pregunta)
                                                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                                                    <p class="text-sm font-semibold text-gray-700 mb-3">{{ $pregunta }}</p>
                                                    <div class="flex gap-6">
                                                        <label class="flex items-center cursor-pointer group">
                                                            <input type="radio" name="habitos[{{ $key }}]" value="si" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" required>
                                                            <span class="ml-2 text-sm text-gray-600 group-hover:text-blue-600 transition">Sí</span>
                                                        </label>
                                                        <label class="flex items-center cursor-pointer group">
                                                            <input type="radio" name="habitos[{{ $key }}]" value="no" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" required>
                                                            <span class="ml-2 text-sm text-gray-600 group-hover:text-blue-600 transition">No</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Observaciones Finales --}}
                                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Observaciones Clínicas / Recomendaciones</label>
                                        <textarea name="notes" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Escriba aquí el diagnóstico general o recomendaciones..."></textarea>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button type="submit" id="btn-save" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-10 rounded-lg shadow-lg transition duration-200 transform hover:scale-105">
                                        GUARDAR DIAGNÓSTICO
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script de captura con "Seguro de Tiempo" --}}
    <script>
        document.getElementById('form-odontologia').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('btn-save');
            btn.disabled = true;
            btn.innerHTML = `
                <span class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    PROCESANDO DIAGNÓSTICO...
                </span>`;

            const captureArea = document.getElementById('capture-area');

            // 500ms es el tiempo perfecto: asegura el render y no desespera al usuario
            setTimeout(() => {
                html2canvas(captureArea, {
                    backgroundColor: '#f8fafc',
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    // Forzamos a que html2canvas espere a que todas las imágenes internas carguen
                    allowTaint: true
                }).then(canvas => {
                    document.getElementById('odontograma_imagen').value = canvas.toDataURL('image/png');
                    this.submit();
                }).catch(error => {
                    console.error("Error en captura:", error);
                    this.submit();
                });
            }, 500);
        });
    </script>
</x-app-layout>
