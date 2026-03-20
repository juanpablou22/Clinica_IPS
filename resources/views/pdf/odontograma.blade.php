<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; line-height: 1.4; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3b82f6; pb: 10px; }
        .title { font-weight: bold; font-size: 14px; text-transform: uppercase; margin: 5px 0; }
        .section-title { font-weight: bold; text-align: center; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; }

        /* Estilo de la Carta Dental (Grilla) */
        .odontograma-grid { width: 100%; text-align: center; margin-bottom: 30px; }
        .tooth-box { display: inline-block; width: 35px; margin: 2px; vertical-align: top; }
        .tooth-number { font-size: 9px; font-weight: bold; display: block; margin-bottom: 2px; }

        /* Simulación del diente SVG en CSS para el PDF */
        .tooth-draw { position: relative; width: 25px; height: 25px; border: 1px solid #000; margin: 0 auto; }
        .face { position: absolute; border: 0.5px solid #000; }
        .f-top { top: 0; left: 0; width: 25px; height: 7px; }
        .f-bottom { bottom: 0; left: 0; width: 25px; height: 7px; }
        .f-left { top: 7px; left: 0; width: 7px; height: 11px; }
        .f-right { top: 7px; right: 0; width: 7px; height: 11px; }
        .f-center { top: 7px; left: 7px; width: 11px; height: 11px; }

        /* Colores de los estados */
        .white { background-color: #ffffff; }
        .red { background-color: #dc2626; }
        .blue { background-color: #2563eb; }
        .green { background-color: #16a34a; }
        .gray { background-color: #1f2937; }

        .observations { margin-top: 20px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <div style="color: #3b82f6; font-style: italic; font-weight: bold;">I.P.S CREAR INTEGRAL S.A.S</div>
        <div>NIT 900727545-8</div>
        <div class="title">TAMIZ VISUAL Y ODONTOLÓGICO</div>
    </div>

    <div>
        <strong>Paciente:</strong> {{ $student->full_name }} <br>
        <strong>Documento:</strong> {{ $student->document_number }} <br>
        <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
    </div>

    <div class="section-title">CARTA DENTAL</div>

    <div class="odontograma-grid">
        @php
            $quadrants = [
                [18,17,16,15,14,13,12,11, 21,22,23,24,25,26,27,28], // Superiores
                [55,54,53,52,51, 61,62,63,64,65],                // Temporales Sup
                [85,84,83,82,81, 71,72,73,74,75],                // Temporales Inf
                [48,47,46,45,44,43,42,41, 31,32,33,34,35,36,37,38]  // Inferiores
            ];
        @endphp

        @foreach($quadrants as $index => $row)
            <div style="margin-bottom: 10px;">
                @foreach($row as $n)
                    <div class="tooth-box">
                        <span class="tooth-number">{{ $n }}</span>
                        <div class="tooth-draw">
                            <div class="face f-top {{ $results['odontograma'][$n]['top'] ?? 'white' }}"></div>
                            <div class="face f-left {{ $results['odontograma'][$n]['left'] ?? 'white' }}"></div>
                            <div class="face f-center {{ $results['odontograma'][$n]['center'] ?? 'white' }}"></div>
                            <div class="face f-right {{ $results['odontograma'][$n]['right'] ?? 'white' }}"></div>
                            <div class="face f-bottom {{ $results['odontograma'][$n]['bottom'] ?? 'white' }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($index == 1) <hr style="border: 0.5px dashed #ccc;"> @endif
        @endforeach
    </div>

    <div class="observations">
        <strong>OBSERVACIONES / RECOMENDACIONES:</strong>
        <p>{{ $notes ?? 'Paciente en buen estado de salud oral.' }}</p>
    </div>

    <div class="footer">
        Dirección: Carrera 12 No 13-24 / Simón Bolívar - Jamundí (Valle) <br>
        Teléfono: 316 185 57 27
    </div>
</body>
</html>
