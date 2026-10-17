<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class AuditService
{
    public static function log($action, $model = null, $data = [])
    {
        try {
            $logData = [
                'user_id' => session('usuario_id', 'N/A'),
                'user_name' => session('usuario_nombre', 'Sistema'),
                'user_role' => session('usuario_rol', 'N/A'),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'user_agent' => request()->userAgent(),
                'model' => $model ? get_class($model) : null,
                'model_id' => $model ? $model->getKey() : null,
                'data' => $data
            ];

            Log::channel('audit')->info($action, $logData);
            
            // Backup: escribir directamente al archivo si falla
            $logPath = storage_path('logs/audit.log');
            $logLine = '[' . now()->format('Y-m-d H:i:s') . '] audit.INFO: ' . $action . ' ' . json_encode($logData) . "\n";
            File::append($logPath, $logLine);
            
        } catch (\Exception $e) {
            // Silenciar errores de logging
        }
    }

    public static function created($model, $data = [])
    {
        self::log('CREATED', $model, $data);
    }

    public static function updated($model, $changes = [])
    {
        self::log('UPDATED', $model, ['changes' => $changes]);
    }

    public static function deleted($model)
    {
        self::log('DELETED', $model);
    }

    public static function login($success = true, $credentials = [])
    {
        $action = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';
        $data = ['success' => $success];
        
        if (!$success && !empty($credentials)) {
            $data['attempted_credentials'] = [
                'email' => $credentials['username'] ?? null,
                'nacionalidad' => $credentials['nacionalidad'] ?? null,
                'password_length' => isset($credentials['password']) ? strlen($credentials['password']) : 0
            ];
        }
        
        self::log($action, null, $data);
    }

    public static function logout()
    {
        self::log('LOGOUT');
    }

    public static function critical($action, $data = [])
    {
        self::log('CRITICAL_' . $action, null, $data);
    }

    public static function security($action, $data = [])
    {
        self::log('SECURITY_' . $action, null, $data);
    }

    public static function access($action, $resource = null, $data = [])
    {
        $logData = array_merge($data, ['resource' => $resource]);
        self::log('ACCESS_' . $action, null, $logData);
    }
}