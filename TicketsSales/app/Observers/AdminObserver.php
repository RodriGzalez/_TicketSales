<?php

namespace App\Observers;

use App\Models\Admin; // O tu modelo correspondiente
use App\Traits\LogsAdminActivity;

class AdminObserver
{
    use LogsAdminActivity;

    public function created(Admin $admin): void
    {
        self::logActivity('CREATED', 'Administradores', "Creó al administrador '{$admin->name}'");
    }

    public function updated(Admin $admin): void
    {
        self::logActivity('UPDATED', 'Administradores', "Actualizó los datos o rol del administrador '{$admin->name}'");
    }

    public function deleted(Admin $admin): void
    {
        self::logActivity('DELETED', 'Administradores', "Eliminó al administrador '{$admin->name}'");
    }
}