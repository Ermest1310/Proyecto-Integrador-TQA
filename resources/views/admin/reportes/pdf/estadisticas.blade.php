<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { color: #007bff; text-align: center; }
        h2 { color: #28a745; margin-top: 30px; border-bottom: 2px solid #28a745; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        th { background: #007bff; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .fecha { text-align: center; color: #666; margin-bottom: 20px; }
        .stat-box { display: inline-block; width: 48%; padding: 10px; margin: 5px 0; background: #f8f9fa; border-left: 4px solid #007bff; }
        .stat-label { font-weight: bold; color: #666; }
        .stat-value { font-size: 24px; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <p class="fecha">Generado: {{ now()->format('d/m/Y H:i') }}</p>
    
    <h2>Usuarios</h2>
    <div class="stat-box">
        <div class="stat-label">Total Usuarios</div>
        <div class="stat-value">{{ $datos['usuarios']['total'] }}</div>
    </div>
    <div class="stat-box">
        <div class="stat-label">Usuarios Activos</div>
        <div class="stat-value">{{ $datos['usuarios']['activos'] }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Rol</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos['usuarios']['por_rol'] as $rol => $cantidad)
            <tr>
                <td>{{ $rol }}</td>
                <td>{{ $cantidad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Niveles de Gimnastas</h2>
    <table>
        <thead>
            <tr>
                <th>Nivel</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos['niveles']['por_nivel'] as $nivel => $cantidad)
            <tr>
                <td>{{ $nivel }}</td>
                <td>{{ $cantidad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="stat-box">
        <div class="stat-label">Solicitudes Pendientes</div>
        <div class="stat-value">{{ $datos['niveles']['solicitudes_pendientes'] }}</div>
    </div>
    <div class="stat-box">
        <div class="stat-label">Solicitudes Aprobadas</div>
        <div class="stat-value">{{ $datos['niveles']['solicitudes_aprobadas'] }}</div>
    </div>

    <h2>Sesiones de Entrenamiento</h2>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total</td>
                <td>{{ $datos['sesiones']['total'] }}</td>
            </tr>
            <tr>
                <td>Programadas</td>
                <td>{{ $datos['sesiones']['programadas'] }}</td>
            </tr>
            <tr>
                <td>Completadas</td>
                <td>{{ $datos['sesiones']['completadas'] }}</td>
            </tr>
            <tr>
                <td>Canceladas</td>
                <td>{{ $datos['sesiones']['canceladas'] }}</td>
            </tr>
            <tr>
                <td>Este Mes</td>
                <td>{{ $datos['sesiones']['este_mes'] }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Postulaciones</h2>
    <table>
        <thead>
            <tr>
                <th>Estado</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total</td>
                <td>{{ $datos['postulaciones']['total'] }}</td>
            </tr>
            <tr>
                <td>Pendientes</td>
                <td>{{ $datos['postulaciones']['pendientes'] }}</td>
            </tr>
            <tr>
                <td>Aprobadas</td>
                <td>{{ $datos['postulaciones']['aprobadas'] }}</td>
            </tr>
            <tr>
                <td>Rechazadas</td>
                <td>{{ $datos['postulaciones']['rechazadas'] }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Lesiones</h2>
    <div class="stat-box">
        <div class="stat-label">Total Lesiones</div>
        <div class="stat-value">{{ $datos['lesiones']['total'] }}</div>
    </div>
    <div class="stat-box">
        <div class="stat-label">Lesiones Activas</div>
        <div class="stat-value">{{ $datos['lesiones']['activas'] }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos['lesiones']['por_tipo'] as $tipo => $cantidad)
            <tr>
                <td>{{ $tipo }}</td>
                <td>{{ $cantidad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
