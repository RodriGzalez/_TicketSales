<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GraduFest - Boletos')</title>
    <!-- Carga de CSS y JS con Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- Navbar Global -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-black text-indigo-600">🎓 GraduFest</a>
            <div>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-indigo-600 mr-4">Mis Boletos</a>
                <a href="#" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700">Iniciar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Barra de Categorías / Filtros Rápidos -->
<nav class="bg-gray-900 border-b border-gray-800 text-sm">
    <div class="max-w-7xl mx-auto px-4 flex items-center space-x-6 overflow-x-auto py-3">
        <a href="#" class="flex items-center gap-2 text-white font-semibold bg-indigo-600 px-4 py-2 rounded-xl whitespace-nowrap shadow-md shadow-indigo-600/20">
            🎟️ Todos los Eventos
        </a>
        <a href="#" class="flex items-center gap-2 text-gray-300 hover:text-white px-3 py-2 rounded-lg whitespace-nowrap transition">
            🏛️ Universidades
        </a>
        <a href="#" class="flex items-center gap-2 text-gray-300 hover:text-white px-3 py-2 rounded-lg whitespace-nowrap transition">
            🎓 Bachilleratos / Preparatorias
        </a>
        <a href="#" class="flex items-center gap-2 text-gray-300 hover:text-white px-3 py-2 rounded-lg whitespace-nowrap transition">
            🏫 Secundarias & Primarias
        </a>
    </div>
</nav>

    <!-- Contenido dinámico de cada vista -->
    <main>
        @yield('content')
    </main>

    <!-- Footer Global -->
    <footer class="bg-white border-t border-gray-200 mt-16 py-6 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} GraduFest. Todos los derechos reservados.
    </footer>

</body>
</html>