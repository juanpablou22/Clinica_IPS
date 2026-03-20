<x-app-layout>
    <div class="py-12" x-data="medForm()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('medical_exams.store_result', $medicalExam) }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter">1. Medidas Antropométricas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-slate-50 p-4 rounded-2xl">
                            <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso" x-model="peso" @input="calc()" class="w-full border-none bg-transparent text-xl font-bold focus:ring-0">
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl">
                            <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Talla (cm)</label>
                            <input type="number" name="talla" x-model="talla" @input="calc()" class="w-full border-none bg-transparent text-xl font-bold focus:ring-0">
                        </div>
                        <div class="bg-blue-600 p-4 rounded-2xl text-white text-center">
                            <label class="text-[10px] font-bold text-blue-200 uppercase mb-1 block">IMC Calculado</label>
                            <span class="text-3xl font-black" x-text="imc">0.0</span>
                            <input type="hidden" name="imc" :value="imc">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <template x-for="s in ['Bajo Peso', 'Normal', 'Sobrepeso', 'Obesidad']">
                            <label class="cursor-pointer">
                                <input type="radio" name="imc_status" :value="s" x-model="status" class="peer hidden">
                                <div class="py-3 rounded-xl border-2 border-slate-100 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <span class="text-[10px] font-black uppercase text-slate-400 peer-checked:text-blue-600" x-text="s"></span>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center tracking-tighter uppercase">
                        <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-xs">2</span>
                        Antecedentes Médicos
                    </h3>
                    <div class="space-y-4">
                        @php
                            $preguntas = [
                                'enf' => '¿El niño tiene alguna enfermedad diagnosticada?',
                                'hosp' => '¿Ha sido hospitalizado alguna vez?',
                                'cir' => '¿Ha tenido cirugías?',
                                'med' => '¿Toma medicamentos actualmente?',
                                'ale' => '¿Tiene alergias conocidas?',
                                'cans' => '¿Se cansa fácilmente al hacer actividad física?',
                                'nacio' => '¿El niño nació: (A término / Prematuro)?',
                                'bajo_p' => '¿Tuvo bajo peso al nacer?',
                                'ped' => '¿El desarrollo ha sido normal según su pediatra?',
                                'ret' => '¿Ha tenido retraso en: (Lenguaje, Aprendizaje, Motricidad)?'
                            ];
                        @endphp
                        @foreach($preguntas as $key => $txt)
                        <div class="p-4 rounded-2xl border border-slate-50 flex flex-col md:flex-row md:items-center gap-4">
                            <p class="flex-1 text-sm font-bold text-slate-600">{{ $loop->iteration }}. {{ $txt }}</p>
                            <div class="flex items-center gap-4">
                                <label class="text-xs font-bold text-slate-400"><input type="radio" name="q_{{$key}}" value="No" checked class="text-blue-600"> No</label>
                                <label class="text-xs font-bold text-slate-400"><input type="radio" name="q_{{$key}}" value="Si" class="text-blue-600"> Sí →</label>
                                <input type="text" name="det_{{$key}}" placeholder="¿Cuál?" class="border-b border-slate-200 bg-transparent text-xs focus:ring-0 focus:border-blue-500 outline-none w-32">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 uppercase tracking-tighter">3. Exploración Física</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach(['Cabeza', 'Cuello', 'Torax', 'Abdomen', 'Extremidades'] as $item)
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase mb-2 block tracking-widest">{{ $item }}</label>
                            <textarea name="f_{{strtolower($item)}}" rows="2" class="w-full bg-slate-50 border-none rounded-2xl text-sm focus:ring-blue-500" placeholder="Escriba los hallazgos..."></textarea>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-blue-600 text-white px-12 py-4 rounded-2xl font-black shadow-xl shadow-blue-100 hover:scale-105 transition-all">
                        FINALIZAR VALORACIÓN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function medForm() {
            return {
                peso: '', talla: '', imc: '0.0', status: '',
                calc() {
                    if(this.peso > 0 && this.talla > 0) {
                        let tallM = this.talla / 100;
                        let res = (this.peso / (tallM * tallM)).toFixed(1);
                        this.imc = res;
                        if(res < 18.5) this.status = 'Bajo Peso';
                        else if(res < 24.9) this.status = 'Normal';
                        else if(res < 29.9) this.status = 'Sobrepeso';
                        else this.status = 'Obesidad';
                    }
                }
            }
        }
    </script>
</x-app-layout>