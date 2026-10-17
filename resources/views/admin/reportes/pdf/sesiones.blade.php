<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #007bff; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #007bff; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .fecha { text-align: center; color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <p class="fecha">Generado: {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Gimnasta</th>
                <th>Entrenador</th>
                <th>Aparato</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $sesion)
            <tr>
                <td>{{ \Carbon\Carbon::parse($sesion->Fecha)->format('d/m/Y') }}</td>
                <td>{{ $sesion->gimnasta }}</td>
                <td>{{ $sesion->entrenador }}</td>
                <td>{{ $sesion->aparato }}</td>
                <td>{{ $sesion->Estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
