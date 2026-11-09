<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - GraduFest')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar de Administración Oculto -->
       <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex border-r border-gray-800">
    <!-- Logo / Header -->
        <div class="p-6 text-xl font-extrabold tracking-wider text-indigo-400 flex items-center justify-between">
            <span>GraduFest</span>
            <span class="text-[10px] bg-indigo-600 text-white px-2 py-0.5 rounded-md font-semibold tracking-wide uppercase">
                {{ auth('admin')->user()->role ?? 'Admin' }}
            </span>
        </div>

        <!-- Navegación Principal -->
        <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
            
            @if(auth('admin')->check())
                @php
                    $role = auth('admin')->user()->role;
                @endphp

                <!-- Menú Operativo (Visible para suadmin y staff) -->
                @if(in_array($role, ['suadmin', 'staff']))
                    <div class="pt-4 pb-2 text-[10px] font-black tracking-wider text-gray-400 uppercase px-3">
                        Menú Principal
                    </div>
                    
                    <a href="{{ route('admin.events.tickets.global') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition group">
                        <span class="text-base">📊</span>
                        <span>Concentrado</span>
                    </a>
                @endif

                <!-- Escáner de Accesos (Visible para los 3: suadmin, staff y checker) -->
                @if(in_array($role, ['suadmin', 'staff', 'checker']))
                    @if($role === 'checker')
                        <div class="pt-4 pb-2 text-[10px] font-black tracking-wider text-gray-400 uppercase px-3">
                            Acceso Rápido
                        </div>
                    @endif

                    <a href="{{ route('admin.scanner.index') }}" 
                        class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition {{ request()->routeIs('admin.scanner.*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 16h1v1h-1v-1zm-6 0h.01M10 12h.01M10 8h.01M14 8h.01M16 12h.01M18 8h.01M6 12h.01M6 16h.01M4 8h.01M4 16h.01M4 12h.01" />
                        </svg>
                        Escáner de Accesos
                    </a>
                @endif

                <!-- Opciones exclusivas para Super Administradores (suadmin) -->
                @if($role === 'suadmin')
                    <div class="pt-4 pb-2 text-[10px] font-black tracking-wider text-gray-400 uppercase px-3">
                        Administración
                    </div>
                    
                    <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition group">
                        <span class="text-base">📅</span>
                        <span>Gestionar Eventos</span>
                    </a>

                    <a href="{{ route('admin.admins.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition group">
                        <span class="text-base">👥</span>
                        <span>Usuarios Admin</span>
                    </a>

                    <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition group">
                        <span class="text-base">📜</span>
                        <span>Bitácora de Logs</span>
                    </a>
                @endif

            @endif    
        <!-- Sitio Público (Visible para todos) -->
    <div class="pt-4 pb-2 text-[10px] font-black tracking-wider text-gray-400 uppercase px-3">
        Sitio
    </div>
    
    <a href="{{ route('events.index') }}" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-400 hover:bg-gray-800 hover:text-white transition group">
        <span class="text-base">🌐</span>
        <span>Ver Sitio Público ↗</span>
    </a>
</nav>

    <!-- Cerrar Sesión -->
    @auth('admin')
    <div class="p-4 border-t border-gray-800 bg-gray-900/50">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-xl transition">
                <span>🚪</span>
                <span>Cerrar Sesión</span>
            </button>
        </form>
    </div>
    @endauth
</aside>
        <!-- Contenido Principal -->
        <main class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8">
                <h1 class="text-lg font-bold text-gray-800">Panel de Control Interno</h1>
               @auth
                    <span class="text-sm text-gray-600 font-medium">Hola, {{ Auth::user()->name }}</span>
                @endauth
            </header>
            <div class="p-8 flex-1">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>