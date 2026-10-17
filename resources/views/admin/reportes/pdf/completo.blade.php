<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #007bff; text-align: center; }
        h2 { color: #333; margin-top: 20px; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background-color: #007bff; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .fecha { text-align: right; color: #666; font-size: 10px; }
        .badge { padding: 3px 8px; border-radius: 3px; color: white; font-size: 10px; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p class="fecha">Generado: {{ $datos['fecha'] }}</p>
    </div>

    <h2>Gimnastas y Rendimiento en Sesiones</h2>
    <table>
        <thead>
            <tr>
                <th>Gimnasta</th>
                <th>Nivel</th>
                <th>Total Sesiones</th>
                <th>Completadas</th>
                <th>% Completadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos['gimnastas_sesiones'] as $g)
            <tr>
                <td>{{ $g->Nombre }} {{ $g->Apellido }}</td>
                <td>{{ $g->Nivel }}</td>
                <td>{{ $g->total_sesiones }}</td>
                <td>{{ $g->sesiones_completadas }}</td>
                <td>{{ $g->total_sesiones > 0 ? round(($g->sesiones_completadas / $g->total_sesiones) * 100) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Lesiones Activas</h2>
    <table>
        <thead>
            <tr>
                <th>Gimnasta</th>
                <th>Tipo</th>
                <th>Parte del Cuerpo</th>
                <th>Gravedad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($datos['lesiones_activas'] as $l)
            <tr>
                <td>{{ $l->Nombre }} {{ $l->Apellido }}</td>
                <td>{{ $l->Tipo }}</td>
                <td>{{ $l->ParteCuerpo }}</td>
                <td>{{ $l->Gravedad }}</td>
                <td>{{ $l->Estado }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No hay lesiones activas</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Rendimiento por Nivel</h2>
    <table>
        <thead>
            <tr>
                <th>Nivel</th>
                <th>Total Sesiones</th>
                <th>% Completadas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos['rendimiento_nivel'] as $n)
            <tr>
                <td>{{ $n->Nivel }}</td>
                <td>{{ $n->total_sesiones }}</td>
                <td>{{ round($n->porcentaje_completadas) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
