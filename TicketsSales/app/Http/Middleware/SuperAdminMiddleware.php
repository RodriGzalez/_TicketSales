<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Validamos si el guardia admin logueado es suadmin
        if (auth('admin')->check() && auth('admin')->user()->role === 'suadmin') {
            return $next($request);
        }

        return redirect()->route('admin.events.index')
            ->with('error', 'Acceso restringido solo para Super Administradores.');
    }
}
