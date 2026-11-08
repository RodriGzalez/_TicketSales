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
        <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
            <div class="p-6 text-xl font-extrabold tracking-wider text-indigo-400">
                GraduFest <span class="text-xs bg-indigo-600 text-white px-2 py-0.5 rounded ml-1">Admin</span>
            </div>
            <nav class="flex-1 px-4 space-y-2">
                <a href="{{ route('admin.events.index') }}" class="block px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-medium">📅 Gestionar Eventos</a>
                <a href="{{ route('admin.events.tickets.global') }}"class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl text-sm transition shadow-sm inline-flex items-center gap-2"> 📊 Concentrado de Boletos </a>
                <a href="{{ route('events.index') }}" target="_blank" class="block px-4 py-2.5 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition">🌐 Ver Sitio Público ↗</a>
            </nav>
            <div class="p-4 border-t border-gray-800">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-gray-800 rounded-lg transition">Cerrar Sesión</button>
                </form>
            </div>
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