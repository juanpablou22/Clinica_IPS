@props(['number'])

<div x-data="{
    {{-- Estado independiente para cada una de las 5 caras --}}
    faces: {
        top: 'white',
        bottom: 'white',
        left: 'white',
        right: 'white',
        center: 'white'
    },
    {{-- Función para rotar colores por cada clic en una cara específica --}}
    toggleFace(face) {
        const states = ['white', 'red', 'blue', 'gray'];
        let currentIndex = states.indexOf(this.faces[face]);
        this.faces[face] = states[(currentIndex + 1) % states.length];
    },
    {{-- Colores dinámicos para el SVG --}}
    getColor(face) {
        return {
            'white': '#f8fafc', {{-- Sano --}}
            'red': '#dc2626',   {{-- Caries --}}
            'blue': '#2563eb',  {{-- Tratado --}}
            'gray': '#4b5563'   {{-- Ausente --}}
        }[this.faces[face]];
    }
}" class="flex flex-col items-center gap-1 group">

    {{-- SVG interactivo que simula la carta dental --}}
    <svg width="45" height="45" viewBox="0 0 100 100" class="drop-shadow-sm transition-transform group-hover:scale-110">
        <path @click="toggleFace('top')" :fill="getColor('top')" d="M10,10 L90,10 L70,30 L30,30 Z" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        <path @click="toggleFace('right')" :fill="getColor('right')" d="M90,10 L90,90 L70,70 L70,30 Z" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        <path @click="toggleFace('bottom')" :fill="getColor('bottom')" d="M10,90 L90,90 L70,70 L30,70 Z" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        <path @click="toggleFace('left')" :fill="getColor('left')" d="M10,10 L10,90 L30,70 L30,30 Z" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        <rect @click="toggleFace('center')" :fill="getColor('center')" x="30" y="30" width="40" height="40" stroke="#cbd5e1" stroke-width="2" class="cursor-pointer hover:opacity-80" />

        <text x="50" y="55" font-family="Arial" font-size="12" font-weight="bold" fill="#1e293b" text-anchor="middle" pointer-events="none">
            {{ $number }}
        </text>
    </svg>

    {{-- Inputs ocultos para enviar el estado de cada cara al controlador --}}
    <template x-for="(color, face) in faces">
        <input type="hidden" :name="'results[odontograma][{{ $number }}][' + face + ']'" :value="color">
    </template>
</div>
