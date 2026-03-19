<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Matricular Nuevo Estudiante') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-8 text-gray-900">

                    {{-- BLOQUE DE ERRORES: Vital para saber qué falta --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                            <p class="font-bold mb-2">Por favor corrige los siguientes errores:</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('students.store') }}" class="space-y-8">
                        @csrf

                        {{-- SECCIÓN 1: DATOS DEL ESTUDIANTE --}}
                        <div class="border-b border-gray-100 pb-6">
                            <h3 class="text-lg font-medium text-blue-600 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Información del Estudiante
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="document_type" :value="__('Tipo Documento')" />
                                    <select id="document_type" name="document_type" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block mt-1 w-full" required>
                                        <option value="TI" {{ old('document_type') == 'TI' ? 'selected' : '' }}>T.I</option>
                                        <option value="RC" {{ old('document_type') == 'RC' ? 'selected' : '' }}>R.C</option>
                                        <option value="CC" {{ old('document_type') == 'CC' ? 'selected' : '' }}>C.C</option>
                                    </select> {{-- CORREGIDO: Faltaba cerrar el select --}}
                                </div> {{-- CORREGIDO: Faltaba cerrar el div --}}

                                <div>
                                    <x-input-label for="document_number" :value="__('Número Documento')" />
                                    <x-text-input id="document_number" class="block mt-1 w-full" type="text" name="document_number" :value="old('document_number')" required />
                                </div>
                                <div>
                                    <x-input-label for="first_name" :value="__('Nombres')" />
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required />
                                </div>
                                <div>
                                    <x-input-label for="last_name" :value="__('Apellidos')" />
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
                                </div>
                                <div>
                                    <x-input-label for="age" :value="__('Edad')" />
                                    <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age')" required />
                                </div>
                                <div>
                                    <x-input-label for="gender" :value="__('Sexo')" />
                                    <select id="gender" name="gender" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block mt-1 w-full" required>
                                        <option value="Masculino" {{ old('gender') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Femenino" {{ old('gender') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                        <option value="Otro" {{ old('gender') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="previous_school" :value="__('Colegio de Procedencia')" />
                                    <x-text-input id="previous_school" class="block mt-1 w-full" type="text" name="previous_school" :value="old('previous_school')" required />
                                </div>
                                <div>
                                    <x-input-label for="grade" :value="__('Grado al que aplica')" />
                                    <select id="grade" name="grade" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block mt-1 w-full" required>
                                        @foreach(['Transición', '1° Primaria', '2° Primaria', '3° Primaria', '4° Primaria', '5° Primaria', '6° Bachillerato', '7° Bachillerato', '8° Bachillerato', '9° Bachillerato', '10° Bachillerato', '11° Bachillerato'] as $g)
                                            <option value="{{ $g }}" {{ old('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: INFORMACIÓN DEL ACUDIENTE --}}
                        <div class="border-b border-gray-100 pb-6">
                            <h3 class="text-lg font-medium text-blue-600 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Información del Acudiente
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="guardian_name" :value="__('Nombres')" />
                                    <x-text-input id="guardian_name" class="block mt-1 w-full" type="text" name="guardian_name" :value="old('guardian_name')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_lastname" :value="__('Apellidos')" />
                                    <x-text-input id="guardian_lastname" class="block mt-1 w-full" type="text" name="guardian_lastname" :value="old('guardian_lastname')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_document" :value="__('Identificación')" />
                                    <x-text-input id="guardian_document" class="block mt-1 w-full" type="text" name="guardian_document" :value="old('guardian_document')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_age" :value="__('Edad')" />
                                    <x-text-input id="guardian_age" class="block mt-1 w-full" type="number" name="guardian_age" :value="old('guardian_age')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_phone" :value="__('Teléfono')" />
                                    <x-text-input id="guardian_phone" class="block mt-1 w-full" type="text" name="guardian_phone" :value="old('guardian_phone')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_relationship" :value="__('Parentesco')" />
                                    <x-text-input id="guardian_relationship" class="block mt-1 w-full" type="text" name="guardian_relationship" :value="old('guardian_relationship')" required />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="guardian_address" :value="__('Dirección')" />
                                    <x-text-input id="guardian_address" class="block mt-1 w-full" type="text" name="guardian_address" :value="old('guardian_address')" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_email" :value="__('Correo Electrónico')" />
                                    <x-text-input id="guardian_email" class="block mt-1 w-full" type="email" name="guardian_email" :value="old('guardian_email')" required />
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 3: CIRCUITO MÉDICO --}}
                        <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 shadow-sm">
                            <h3 class="text-lg font-bold text-blue-700 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Exámenes Médicos Requeridos
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-y-4 gap-x-6">
                                @php
                                    $especialidades = [
                                        'valoracion_medica' => 'Valoración Médica',
                                        'odontologia' => 'Odontología',
                                        'optometria' => 'Optometría',
                                        'fonoaudiologia' => 'Fonoaudiología',
                                        'audiometria' => 'Audiometría',
                                        'psicologia' => 'Psicología'
                                    ];
                                @endphp

                                @foreach($especialidades as $value => $label)
                                    <label class="relative flex items-center p-3 rounded-lg border border-white hover:bg-white hover:shadow-sm cursor-pointer transition">
                                        <input type="checkbox" name="requested_areas[]" value="{{ $value }}"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-5 h-5"
                                            {{ (is_array(old('requested_areas')) && in_array($value, old('requested_areas'))) || !old('requested_areas') ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- BOTONES DE ACCIÓN --}}
                        <div class="flex items-center justify-end space-x-4 pt-6">
                            <a href="{{ route('students.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                                Cancelar
                            </a>
                            <x-primary-button class="bg-blue-700 hover:bg-blue-800 px-8 py-3">
                                {{ __('Finalizar Matrícula') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
