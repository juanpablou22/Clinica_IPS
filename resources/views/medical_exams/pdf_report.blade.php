<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #334155; margin: 0; padding: 0; }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .section-title { font-size: 14px; font-weight: bold; background: #f1f5f9; padding: 8px; border-left: 5px solid #2563eb; margin: 20px 0 10px; color: #1e3a8a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; font-size: 11px; text-align: left; }
        th { background: #f8fafc; font-weight: bold; width: 30%; }
        .badge { background: #dbeafe; color: #1e40af; padding: 5px 10px; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 22px;">I.P.S CREAR INTEGRAL S.A.S</h1>
        <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.8;">REPORTE DE VALORACIÓN MÉDICA INTEGRAL</p>
    </div>

    <div class="content">
        <div class="section-title">DATOS DEL PACIENTE</div>
        <table>
            <tr><th>Nombre Completo:</th><td>{{ $student->name }}</td></tr>
            <tr><th>Identificación:</th><td>{{ $student->document_number }}</td></tr>
        </table>

        <div class="section-title">ESTADO NUTRICIONAL (IMC)</div>
        <table>
            <tr>
                <th>Peso:</th><td>{{ $data['peso'] }} kg</td>
                <th>Talla:</th><td>{{ $data['talla'] }} cm</td>
            </tr>
            <tr>
                <th>IMC:</th><td>{{ $data['imc'] }}</td>
                <th>Resultado:</th><td><span class="badge">{{ $data['imc_status'] }}</span></td>
            </tr>
        </table>

        <div class="section-title">ANTECEDENTES RELEVANTES</div>
        <table>
            @foreach(['enf' => 'Enfermedades', 'ale' => 'Alergias', 'cir' => 'Cirugías', 'ret' => 'Retraso Desarrollo'] as $k => $label)
            <tr>
                <th>{{ $label }}:</th>
                <td>{{ $data['q_'.$k] }} - {{ $data['det_'.$k] ?? 'Ninguno' }}</td>
            </tr>
            @endforeach
        </table>

        <div class="section-title">HALLAZGOS EXAMEN FÍSICO</div>
        <table>
            @foreach(['cabeza', 'cuello', 'torax', 'abdomen', 'extremidades'] as $f)
            <tr>
                <th>{{ ucfirst($f) }}:</th>
                <td>{{ $data['f_'.$f] ?: 'Normal / Sin hallazgos' }}</td>
            </tr>
            @endforeach
        </table>

        <div style="margin-top: 80px; text-align: center;">
            <p style="border-top: 1px solid #94a3b8; width: 250px; margin: 0 auto; padding-top: 10px; font-size: 11px;">
                FIRMA DEL MÉDICO: {{ $doctor->name }}<br>
                REGISTRO PROFESIONAL I.P.S CREAR INTEGRAL
            </p>
        </div>
    </div>
</body>
</html>