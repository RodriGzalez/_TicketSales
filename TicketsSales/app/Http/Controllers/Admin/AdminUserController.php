<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\LogsAdminActivity; // 👈 1. Importante: Importar el Trait

class AdminUserController extends Controller
{
    use LogsAdminActivity; // 👈 2. Usar el Trait en el controlador

    public function index()
    {
        if (auth('admin')->user()->role !== 'suadmin') {
            return redirect()->route('admin.events.index')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $admins = Admin::all();
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:suadmin,staff,checker',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // 📝 Registrar log de creación
        self::logActivity(
            'CREATED',
            'Administradores',
            "Creó al administrador '{$admin->name}' ({$admin->email})"
        );

        return redirect()->route('admin.admins.index')
            ->with('success', '¡Usuario administrador creado exitosamente!');
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,' . $admin->id,
            'role' => 'required|string|in:suadmin,staff,checker',
            'password' => 'nullable|string|min:8',
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->role = $validated['role'];

        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        // 📝 Registrar log de actualización
        self::logActivity(
            'UPDATED',
            'Administradores',
            "Modificó los datos/rol del administrador '{$admin->name}'"
        );

        return redirect()->route('admin.admins.index')
            ->with('success', '¡Administrador actualizado exitosamente!');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->id === auth('admin')->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        $adminName = $admin->name;
        $admin->delete();

        // 📝 Registrar log de eliminación
        self::logActivity(
            'DELETED',
            'Administradores',
            "Eliminó al administrador '{$adminName}'"
        );

        return redirect()->route('admin.admins.index')
            ->with('success', 'Administrador eliminado correctamente.');
    }
}