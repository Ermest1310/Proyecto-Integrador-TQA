<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        \App\Services\PermissionService::abort('admin.audit');
        
        // Registrar acceso a auditoría
        \App\Services\AuditService::access('AUDIT_VIEW', 'audit_logs');
        

        
        $logPath = storage_path('logs/audit.log');
        $logs = [];
        $debug = [
            'file_exists' => File::exists($logPath),
            'file_path' => $logPath,
            'file_size' => File::exists($logPath) ? File::size($logPath) : 0
        ];
        
        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $lines = array_reverse(explode("\n", trim($content)));
            
            foreach ($lines as $line) {
                if (empty($line)) continue;
                
                $logEntry = $this->parseLogLine($line);
                if ($logEntry && !in_array($logEntry['action'], ['TEST_AUDIT_VIEW', 'ACCESS_AUDIT_VIEW'])) {
                    $logs[] = $logEntry;
                }
            }
        }
        
        // Filtros
        if ($request->filled('action')) {
            $logs = array_filter($logs, function($log) use ($request) {
                return stripos($log['action'], $request->action) !== false;
            });
        }
        
        if ($request->filled('user')) {
            $logs = array_filter($logs, function($log) use ($request) {
                return stripos($log['user_name'], $request->user) !== false;
            });
        }
        
        if ($request->filled('date')) {
            $logs = array_filter($logs, function($log) use ($request) {
                return date('Y-m-d', strtotime($log['timestamp'])) === $request->date;
            });
        }
        
        if ($request->filled('severity')) {
            $logs = array_filter($logs, function($log) use ($request) {
                return $log['severity'] === $request->severity;
            });
        }
        

        
        // Paginación manual
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = count($logs);
        $logs = array_slice($logs, ($page - 1) * $perPage, $perPage);
        
        // Estadísticas
        $stats = [
            'total_events' => $total,
            'login_failures' => count(array_filter($logs, fn($log) => $log['action'] === 'LOGIN_FAILED')),
            'security_events' => count(array_filter($logs, fn($log) => str_contains($log['action'], 'SECURITY'))),
            'critical_events' => count(array_filter($logs, fn($log) => str_contains($log['action'], 'CRITICAL')))
        ];
        
        return view('admin.audit', compact('logs', 'total', 'page', 'perPage', 'debug', 'stats'));
    }
    
    private function parseLogLine($line)
    {
        // Patrón más flexible para capturar diferentes formatos de log
        if (!preg_match('/\[(.*?)\] audit\.INFO: ([A-Z_]+) (.*)/', $line, $matches)) {
            return null;
        }
        
        $timestamp = $matches[1];
        $action = $matches[2];
        $jsonData = $matches[3];
        
        $data = json_decode($jsonData, true);
        if (!$data) {
            // Si no hay JSON, crear estructura básica
            $data = [];
        }
        
        return [
            'timestamp' => $timestamp,
            'action' => $action,
            'user_id' => $data['user_id'] ?? 'N/A',
            'user_name' => $data['user_name'] ?? 'Sistema',
            'user_role' => $data['user_role'] ?? 'N/A',

            'user_agent' => $data['user_agent'] ?? 'N/A',
            'model' => $data['model'] ?? null,
            'model_id' => $data['model_id'] ?? null,
            'data' => $data['data'] ?? [],
            'severity' => $this->getSeverity($action),
            'description' => $this->getActionDescription($action, $data['data'] ?? [])
        ];
    }
    
    private function getSeverity($action)
    {
        if (str_contains($action, 'CRITICAL')) return 'critical';
        if (str_contains($action, 'SECURITY')) return 'high';
        if (str_contains($action, 'LOGIN_FAILED')) return 'medium';
        if (str_contains($action, 'DELETED')) return 'medium';
        return 'low';
    }
    
    private function getActionDescription($action, $data)
    {
        switch ($action) {
            case 'LOGIN_SUCCESS':
                return 'Inicio de sesión exitoso';
            case 'LOGIN_FAILED':
                $email = $data['attempted_credentials']['email'] ?? 'N/A';
                $nacionalidad = $data['attempted_credentials']['nacionalidad'] ?? 'N/A';
                return "Intento de login fallido - Email: {$email}, Nacionalidad: {$nacionalidad}";
            case 'LOGOUT':
                return 'Cierre de sesión';
            case 'SECURITY_INVALID_PASSWORD':
                return 'Contraseña incorrecta para: ' . ($data['email'] ?? 'N/A');
            case 'SECURITY_USER_NOT_FOUND':
                return 'Usuario no encontrado: ' . ($data['email'] ?? 'N/A');
            case 'SECURITY_RATE_LIMIT_EXCEEDED':
                return 'Límite de intentos excedido';
            case 'CREATED':
                return 'Registro creado';
            case 'UPDATED':
                return 'Registro actualizado';
            case 'DELETED':
                return 'Registro eliminado';
            case 'ACCESS_AUDIT_VIEW':
                return 'Acceso a auditoría';
            case 'CRITICAL_POSTULACION_APPROVED':
                return 'Postulación aprobada';
            case 'CRITICAL_PERMISSION_DENIED':
                return 'Permiso denegado';
            default:
                $translated = str_replace(['_', 'CRITICAL', 'SECURITY', 'ACCESS'], [' ', 'CRÍTICO', 'SEGURIDAD', 'ACCESO'], $action);
                return ucwords(strtolower($translated));
        }
    }
}