<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Diagnóstico de Postulaciones</h2>";

try {
    $postulaciones = \App\Models\Postulacion::orderBy('created_at', 'desc')->get();
    
    echo "<p><strong>Total postulaciones:</strong> " . $postulaciones->count() . "</p>";
    
    if ($postulaciones->count() > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Estado</th><th>Fecha</th></tr>";
        foreach ($postulaciones as $p) {
            echo "<tr>";
            echo "<td>{$p->ID_Postulacion}</td>";
            echo "<td>{$p->Nombre} {$p->Apellido}</td>";
            echo "<td>{$p->Email}</td>";
            echo "<td>{$p->estado}</td>";
            echo "<td>{$p->created_at}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red;'>No hay postulaciones en la base de datos</p>";
    }
    
} catch (\Exception $e) {
    echo "<p style='color:red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
