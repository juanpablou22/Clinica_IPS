<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Panel de Control - I.P.S Crear Integral') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Pacientes Registrados</p>
                            <h3 class="text-2xl font-bold text-slate-800">{{ $totalPacientes }}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-50 rounded-lg text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Certificados Listos</p>
                            <h3 class="text-2xl font-bold text-slate-800">{{ $totalCertificados }}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-50 rounded-lg text-orange-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Pendientes</p>
                            <h3 class="text-2xl font-bold text-orange-600">{{ $pendientes }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-8">
                <h4 class="text-lg font-bold text-slate-800 mb-6">Nuevos pacientes registrados</h4>
                <div style="height: 350px;">
                    <canvas id="pacientesChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Buscamos el elemento Canvas por su ID
        const ctx = document.getElementById('pacientesChart').getContext('2d');
        
        // Creamos una nueva instancia del gráfico
        new Chart(ctx, {
            type: 'line', // Tipo línea (ideal para IPS/Salud)
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr'], // Eje X (meses)
                datasets: [{
                    label: 'Nuevos Pacientes',
                    // MEZCLA: Ene y Feb son fijos, Mar usa el dato REAL de la base de datos
                    data: [5, 12, {{ $totalPacientes }}, 15], 
                    borderColor: '#2563eb', // Azul profesional
                    backgroundColor: 'rgba(37, 99, 235, 0.1)', // Relleno transparente
                    fill: true,
                    tension: 0.4 // Curvatura suave de la línea
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Permite que el gráfico crezca con su contenedor
                plugins: {
                    legend: {
                        display: false // Ocultamos la leyenda para un diseño más limpio
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>