<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--type=incremental}';
    protected $description = 'Crear backup de la base de datos (incremental o completo)';

    public function handle()
    {
        $type = $this->option('type');
        $timestamp = now()->format('Y-m-d_His');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST');
        
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = "{$type}_backup_{$timestamp}.sql";
        $filepath = "{$backupDir}/{$filename}";

        if ($type === 'incremental') {
            $this->info('Creando backup incremental...');
            $lastBackup = $this->getLastBackupDate();
            
            if ($lastBackup) {
                $this->info("Último backup: {$lastBackup}");
                $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} --where=\"updated_at >= '{$lastBackup}'\" > \"{$filepath}\"";
            } else {
                $this->warn('No hay backup previo, creando backup completo...');
                $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"{$filepath}\"";
            }
        } else {
            $this->info('Creando backup completo...');
            $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"{$filepath}\"";
        }

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $size = filesize($filepath);
            $sizeInMB = round($size / 1024 / 1024, 2);
            
            $this->info("✓ Backup creado: {$filename} ({$sizeInMB} MB)");
            
            file_put_contents(
                storage_path('app/backups/backup_log.txt'),
                "[{$timestamp}] {$type} - {$filename} - {$sizeInMB}MB\n",
                FILE_APPEND
            );
            
            $this->cleanOldBackups();
            return 0;
        } else {
            $this->error('✗ Error al crear el backup');
            return 1;
        }
    }

    private function getLastBackupDate()
    {
        $logFile = storage_path('app/backups/backup_log.txt');
        if (!file_exists($logFile)) {
            return null;
        }

        $lines = file($logFile);
        if (empty($lines)) {
            return null;
        }

        $lastLine = trim(end($lines));
        preg_match('/\[(.*?)\]/', $lastLine, $matches);
        
        return $matches[1] ?? null;
    }

    private function cleanOldBackups()
    {
        $backupDir = storage_path('app/backups');
        $files = glob("{$backupDir}/*.sql");
        
        if (count($files) > 10) {
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $filesToDelete = array_slice($files, 0, count($files) - 10);
            foreach ($filesToDelete as $file) {
                unlink($file);
            }
        }
    }
}
