<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportFigRules extends Command
{
    protected $signature = 'fig:mine {--skip-extract : Omite la extraccion del PDF (solo importa el JSON existente)}';

    protected $description = 'Extrae reglas del PDF FIG 2025-2028 y las importa a la tabla deducciones_ejecucion';

    public function handle()
    {
        $jsonPath = base_path('reglas_minadas.json');

        if (!$this->option('skip-extract')) {
            $this->info('Extrayendo reglas del PDF FIG 2025-2028...');
            $scriptPath = base_path('scripts/extract_rules.py');

            if (!file_exists($scriptPath)) {
                $this->error("Script no encontrado: $scriptPath");
                return 1;
            }

            $output = [];
            $returnCode = 0;
            exec("python \"$scriptPath\" 2>&1", $output, $returnCode);

            foreach ($output as $line) {
                $this->line($line);
            }

            if ($returnCode !== 0) {
                $this->error('Error al ejecutar el script de extraccion');
                return 1;
            }
        }

        if (!File::exists($jsonPath)) {
            $this->error("Archivo no encontrado: $jsonPath");
            $this->info('Asegurate de que el script extract_rules.py genero el archivo correctamente');
            return 1;
        }

        $data = json_decode(File::get($jsonPath), true);
        $rules = $data['rules'] ?? [];

        if (empty($rules)) {
            $this->error('No se encontraron reglas en el JSON');
            return 1;
        }

        $this->info("Importando " . count($rules) . " reglas FIG...");

        $bar = $this->output->createProgressBar(count($rules));
        $bar->start();

        $inserted = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($rules as $rule) {
                $exists = DB::table('deducciones_ejecucion')
                    ->where('ID_Aparato', $rule['ID_Aparato'])
                    ->where('Tipo', $rule['tipo'])
                    ->where('Descripcion', $rule['descripcion'])
                    ->where('Valor_Deduccion', $rule['valor'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                DB::table('deducciones_ejecucion')->insert([
                    'ID_Aparato' => $rule['ID_Aparato'],
                    'Tipo' => $rule['tipo'],
                    'Descripcion' => $rule['descripcion'],
                    'Valor_Deduccion' => $rule['valor'],
                    'Criterios' => $rule['criterios'] ?? null,
                ]);
                $inserted++;
                $bar->advance();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nError: " . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Importacion completada:");
        $this->table(
            ['', 'Cantidad'],
            [
                ['Insertadas', $inserted],
                ['Saltadas (duplicadas)', $skipped],
                ['Total en JSON', count($rules)],
            ]
        );

        return 0;
    }
}
