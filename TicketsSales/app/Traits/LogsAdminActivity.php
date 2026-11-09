<?php

namespace App\Traits;

use App\Models\AdminLog;

trait LogsAdminActivity
{
    /**
     * Registra una acción realizada por el administrador en la bitácora.
     */
    public static function logActivity(string $action, string $module, string $description, ?array $changes = null)
    {
        AdminLog::create([
            'admin_id'    => auth('admin')->id(),
            'action'      => $action, // Ej: 'CREATED', 'UPDATED', 'DELETED'
            'module'      => $module, // Ej: 'Eventos', 'Usuarios', etc.
            'description' => $description, // Descripción legible
            'changes'     => $changes, // Array con datos (opcional)
            'ip_address'  => request()->ip(), // IP desde donde hizo la acción
        ]);
    }
}