<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

   public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Usamos Auth::guard('admin') para validar contra la tabla 'admins'
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Obtenemos el usuario administrador autenticado
            $admin = Auth::guard('admin')->user();

            // 🔀 Redirección estricta y directa según el rol
            if ($admin->role === 'checker') {
                return redirect()->route('admin.scanner.index');
            }

            if ($admin->role === 'staff') {
                return redirect()->route('admin.events.tickets.global');
            }

            // Por defecto para 'suadmin' u otros
            return redirect()->route('admin.events.index');
        }

        return back()->withErrors([
            'email' => 'Credenciales de administrador inválidas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}