<?php

namespace App\Services;

class PermissionService
{
    private static $permissions = [
        'Administrador' => [
            'users.create', 'users.edit', 'users.delete', 'users.view',
            'roles.create', 'roles.edit', 'roles.delete', 'roles.view',
            'postulaciones.approve', 'postulaciones.reject', 'postulaciones.view',
            'lesiones.create', 'lesiones.edit', 'lesiones.view',
            'aparatos.create', 'aparatos.edit', 'aparatos.delete', 'aparatos.view',
            'competencias.create', 'competencias.edit', 'competencias.delete', 'competencias.view',
            'entrenamientos.create', 'entrenamientos.edit', 'entrenamientos.delete', 'entrenamientos.view',
            'sesiones.create', 'sesiones.edit', 'sesiones.delete', 'sesiones.view',
            'areamedica.view', 'dashboard.admin', 'admin.audit'
        ],
        'Entrenador' => [
            'gimnastas.create', 'gimnastas.edit', 'gimnastas.view',
            'entrenamientos.create', 'entrenamientos.edit', 'entrenamientos.view',
            'sesiones.create', 'sesiones.edit', 'sesiones.view',
            'lesiones.view', 'dashboard.entrenador'
        ],
        'Médico' => [
            'gimnastas.view', 'lesiones.create', 'lesiones.edit', 'lesiones.view',
            'areamedica.view', 'evaluaciones.create', 'evaluaciones.edit',
            'roles.view',
            'dashboard.medico'
        ],
        'Fisioterapeuta' => [
            'gimnastas.view', 'lesiones.view', 'lesiones.edit', 'lesiones.create',
            'areamedica.view', 'rehabilitacion.create', 'rehabilitacion.edit',
            'roles.view',
            'dashboard.medico'
        ],
        'Gimnasta' => [
            'sesiones.view', 'progreso.view', 'entrenador.view',
            'perfil.edit', 'dashboard.gimnasta'
        ]
    ];

    public static function getPermissionsMatrix(): array
    {
        return self::$permissions;
    }

    public static function can($permission, $role = null)
    {
        $role = $role ?? session('usuario_rol');
        
        if (!$role || !isset(self::$permissions[$role])) {
            return false;
        }
        
        return in_array($permission, self::$permissions[$role]);
    }

    public static function isAdmin($role = null)
    {
        $role = $role ?? session('usuario_rol');
        return $role === 'Administrador';
    }

    public static function canManageUsers($role = null)
    {
        return self::can('users.create', $role);
    }

    public static function canViewDashboard($type, $role = null)
    {
        return self::can("dashboard.{$type}", $role);
    }

    public static function abort($permission, $code = 403)
    {
        if (!self::can($permission)) {
            \App\Services\AuditService::critical('PERMISSION_DENIED', [
                'permission' => $permission,
                'attempted_url' => request()->fullUrl()
            ]);
            abort($code, 'No tienes permisos para esta acción');
        }
    }
}