<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #28a745; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th { background-color: #28a745; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .fecha { text-align: right; color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
        <p class="fecha">Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Nivel</th>
                <th>Peso</th>
                <th>Altura</th>
                <th>Lesiones Activas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $g)
            <tr>
                <td>{{ $g->Nombre }} {{ $g->Apellido }}</td>
                <td>{{ $g->Nivel }}</td>
                <td>{{ $g->Peso }} kg</td>
                <td>{{ $g->Altura }} m</td>
                <td>{{ $g->lesiones_activas }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
