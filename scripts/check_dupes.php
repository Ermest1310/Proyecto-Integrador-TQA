<?php
require 'E:\laragon\www\Proyecto2\vendor\autoload.php';
$app = require_once 'E:\laragon\www\Proyecto2\bootstrap\app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Show count by aparato
$counts = DB::select("
    SELECT a.Nombre, d.Tipo, COUNT(*) as cnt
    FROM deducciones_ejecucion d
    JOIN aparatos a ON d.ID_Aparato = a.ID_Aparato
    GROUP BY a.Nombre, d.Tipo
    ORDER BY a.Nombre, d.Tipo
");
echo "Reglas por aparato:\n";
foreach ($counts as $c) {
    echo "  {$c->Nombre} ({$c->Tipo}): {$c->cnt}\n";
}

echo "\nTotal: " . DB::table('deducciones_ejecucion')->count() . "\n";

// Check for similar descriptions that differ slightly
$rows = DB::select("
    SELECT ID_Deduccion, ID_Aparato, Tipo, Descripcion, Valor_Deduccion, Criterios
    FROM deducciones_ejecucion
    ORDER BY ID_Aparato, Tipo
");
$prev = null;
foreach ($rows as $r) {
    $key = "{$r->ID_Aparato}|{$r->Tipo}|{$r->Descripcion}|{$r->Valor_Deduccion}";
    if (isset($seen[$key])) {
        echo "DUPE: ID {$r->ID_Deduccion} matches ID {$seen[$key]}: {$r->Descripcion} ({$r->Valor_Deduccion})\n";
    }
    $seen[$key] = $r->ID_Deduccion;
}
