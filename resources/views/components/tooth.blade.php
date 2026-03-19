@props(['number'])

<div x-data="{
        status: 'green',
        getNextStatus() {
            const states = ['green', 'red', 'blue', 'gray'];
            let currentIndex = states.indexOf(this.status);
            this.status = states[(currentIndex + 1) % states.length];
        }
    }"
    @click="getNextStatus()"
    class="relative flex flex-col items-center cursor-pointer group">

    {{-- El diente (Círculo interactivo) --}}
    <div :class="{
            'bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.4)]': status === 'green',
            'bg-red-600 shadow-[0_0_10px_rgba(220,38,38,0.6)] animate-pulse': status === 'red',
            'bg-blue-600 shadow-[0_0_10px_rgba(37,99,235,0.4)]': status === 'blue',
            'bg-gray-600': status === 'gray'
        }"
        class="w-10 h-10 rounded-full border-2 border-white/20 flex items-center justify-center transition-all duration-300 transform group-hover:scale-110">
        <span class="text-[10px] font-black text-white" x-text="status === 'gray' ? 'X' : '{{ $number }}'"></span>
    </div>

    {{-- Input oculto para enviar al controlador --}}
    <input type="hidden" name="teeth[{{ $number }}]" :value="status">
</div>
