<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; line-height: 1.4; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .title { font-weight: bold; font-size: 14px; text-transform: uppercase; margin: 5px 0; }
        .section-title { font-weight: bold; text-align: center; margin: 20px 0 10px; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; padding: 5px; }

        /* Contenedor de la imagen capturada */
        .odontograma-container {
            width: 100%;
            text-align: center;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 8px;
        }
        .odontograma-img {
            width: 100%;
            max-height: 450px;
            object-fit: contain;
        }

        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 0; }

        .habitos-list { margin: 10px 0; padding-left: 20px; }
        .observations { margin-top: 20px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; background-color: #f1f5f9; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div style="color: #3b82f6; font-style: italic; font-weight: bold;">I.P.S CREAR INTEGRAL S.A.S</div>
        <div>NIT 900727545-8</div>
        <div class="title">TAMIZ VISUAL Y ODONTOLÓGICO</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%"><strong>Paciente:</strong> {{ $student->full_name }}</td>
            <td width="50%"><strong>Documento:</strong> {{ $student->document_number }}</td>
        </tr>
        <tr>
            <td><strong>Fecha de Evaluación:</strong> {{ $date->format('d/m/Y h:i A') }}</td>
            <td><strong>Sede:</strong> Jamundí (Valle)</td>
        </tr>
    </table>

    <div class="section-title">CARTA DENTAL (ODONTOGRAMA)</div>

    <div class="odontograma-container">
        @if($odontograma_img)
            <img src="{{ $odontograma_img }}" class="odontograma-img">
        @else
            <div style="padding: 50px; color: #94a3b8;">No se registró captura visual del odontograma.</div>
        @endif
    </div>

    @if(count($habitos) > 0)
        <div class="section-title">HÁBITOS REGISTRADOS</div>
        <ul class="habitos-list">
            @foreach($habitos as $habito)
                <li>{{ ucfirst(str_replace('_', ' ', $habito)) }}</li>
            @endforeach
        </ul>
    @endif

    <div class="observations">
        <strong>OBSERVACIONES / RECOMENDACIONES:</strong>
        <p>{{ $notes ?? 'Paciente en buen estado de salud oral.' }}</p>
    </div>

    <div class="footer">
        Dirección: Carrera 12 No 13-24 / Simón Bolívar - Jamundí (Valle) <br>
        Teléfono: 316 185 57 27 - I.P.S CREAR INTEGRAL S.A.S
    </div>
</body>
</html>
