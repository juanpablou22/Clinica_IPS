<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Matrícula: ') }} {{ $student->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">

                {{-- Formulario de Edición --}}
                <form action="{{ route('students.update', $student) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        {{-- SECCIÓN 1: DATOS DEL ESTUDIANTE --}}
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-blue-600 border-b pb-2 uppercase text-sm tracking-wider">Datos del Estudiante</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="document_type" :value="__('Tipo Doc.')" />
                                    <select name="document_type" id="document_type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                                        <option value="RC" {{ old('document_type', $student->document_type) == 'RC' ? 'selected' : '' }}>Registro Civil</option>
                                        <option value="TI" {{ old('document_type', $student->document_type) == 'TI' ? 'selected' : '' }}>Tarjeta Identidad</option>
                                        <option value="CC" {{ old('document_type', $student->document_type) == 'CC' ? 'selected' : '' }}>Cédula</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="document_number" :value="__('Número')" />
                                    <x-text-input id="document_number" class="block mt-1 w-full" type="text" name="document_number" :value="old('document_number', $student->document_number)" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="first_name" :value="__('Nombres')" />
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $student->first_name)" required />
                                </div>
                                <div>
                                    <x-input-label for="last_name" :value="__('Apellidos')" />
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $student->last_name)" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="age" :value="__('Edad')" />
                                    <x-text-input id="age" class="block mt-1 w-full" type="number" name="age" :value="old('age', $student->age)" required />
                                </div>
                                <div>
                                    <x-input-label for="gender" :value="__('Género')" />
                                    <select name="gender" id="gender" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="Masculino" {{ old('gender', $student->gender) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Femenino" {{ old('gender', $student->gender) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                        <option value="Otro" {{ old('gender', $student->gender) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="grade" :value="__('Grado Actual')" />
                                    <x-text-input id="grade" class="block mt-1 w-full bg-blue-50" type="text" name="grade" :value="old('grade', $student->grade)" required />
                                </div>
                                <div>
                                    <x-input-label for="previous_school" :value="__('Colegio Anterior')" />
                                    <x-text-input id="previous_school" class="block mt-1 w-full" type="text" name="previous_school" :value="old('previous_school', $student->previous_school)" required />
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: DATOS DEL ACUDIENTE (8 CAMPOS) --}}
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-green-600 border-b pb-2 uppercase text-sm tracking-wider">Datos del Acudiente</h3>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="guardian_name" :value="__('Nombres')" />
                                    <x-text-input id="guardian_name" class="block mt-1 w-full" type="text" name="guardian_name" :value="old('guardian_name', $student->guardian_name)" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_lastname" :value="__('Apellidos')" />
                                    <x-text-input id="guardian_lastname" class="block mt-1 w-full" type="text" name="guardian_lastname" :value="old('guardian_lastname', $student->guardian_lastname)" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="guardian_document" :value="__('Documento ID')" />
                                    <x-text-input id="guardian_document" class="block mt-1 w-full" type="text" name="guardian_document" :value="old('guardian_document', $student->guardian_document)" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_age" :value="__('Edad')" />
                                    <x-text-input id="guardian_age" class="block mt-1 w-full" type="number" name="guardian_age" :value="old('guardian_age', $student->guardian_age)" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="guardian_phone" :value="__('Teléfono')" />
                                    <x-text-input id="guardian_phone" class="block mt-1 w-full" type="text" name="guardian_phone" :value="old('guardian_phone', $student->guardian_phone)" required />
                                </div>
                                <div>
                                    <x-input-label for="guardian_relationship" :value="__('Parentesco')" />
                                    <x-text-input id="guardian_relationship" class="block mt-1 w-full" type="text" name="guardian_relationship" :value="old('guardian_relationship', $student->guardian_relationship)" required />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="guardian_address" :value="__('Dirección de Residencia')" />
                                <x-text-input id="guardian_address" class="block mt-1 w-full" type="text" name="guardian_address" :value="old('guardian_address', $student->guardian_address)" required />
                            </div>

                            <div>
                                <x-input-label for="guardian_email" :value="__('Correo Electrónico')" />
                                <x-text-input id="guardian_email" class="block mt-1 w-full" type="email" name="guardian_email" :value="old('guardian_email', $student->guardian_email)" required />
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end space-x-4 border-t pt-6">
                        <a href="{{ route('students.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-300 transition">Cancelar</a>
                        <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-md shadow-lg hover:bg-blue-700 transition uppercase text-xs font-bold tracking-widest">
                            Actualizar Información
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
