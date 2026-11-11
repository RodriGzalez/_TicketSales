<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GraduFest')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- Tu Navbar personalizado -->
    @include('layouts.navigation') <!-- O tu navbar actual -->

    <main>
        @isset($slot)
            {{ $slot }}
        @endisset

        @yield('content')
    </main>

<footer class="bg-gray-900 text-gray-400 border-t border-gray-800 pt-12 pb-8 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Grid Principal del Footer -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-12 border-b border-gray-800">
            
            <!-- Columna 1: Marca -->
            <div class="space-y-4 md:col-span-1">
                <div class="flex items-center gap-2">
                    <span class="text-xl font-extrabold tracking-wider text-white">GraduFest</span>
                    <span class="text-[10px] bg-indigo-600 text-white px-2 py-0.5 rounded-md font-semibold">Oficial</span>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed">
                    La plataforma oficial y segura para la venta, gestión y control de accesos para eventos de graduación y clausuras universitarias.
                </p>
            </div>

            <!-- Columna 2: Enlaces Rápidos -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Navegación</h3>
                <ul class="space-y-2.5 text-xs">
                    <li>
                        <a href="{{ route('events.index') }}" class="hover:text-indigo-400 transition">Inicio / Eventos</a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('dashboard') }}" class="hover:text-indigo-400 transition">Mis Boletos</a>
                        </li>
                    @endauth
                    <li>
                        <a href="#eventos" class="hover:text-indigo-400 transition">Próximas Graduaciones</a>
                    </li>
                </ul>
            </div>

            <!-- Columna 3: Legal y Ayuda -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Ayuda y Legal</h3>
                <ul class="space-y-2.5 text-xs">
                    <li>
                        <a href="#" class="hover:text-indigo-400 transition">Términos y Condiciones</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-indigo-400 transition">Aviso de Privacidad</a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-indigo-400 transition">Preguntas Frecuentes (FAQ)</a>
                    </li>
                </ul>
            </div>

            <!-- Columna 4: Contacto / Soporte -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Soporte Técnico</h3>
                <p class="text-xs text-gray-400 mb-2">¿Tienes dudas con tus boletos o pagos? Contáctanos:</p>
                <p class="text-xs font-semibold text-indigo-400 mb-1">support@gradufest.com</p>
                <p class="text-[11px] text-gray-500">Lunes a Viernes de 9:00 AM a 6:00 PM</p>
            </div>

        </div>

        <!-- Barra Inferior (Copyright) -->
        <div class="pt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-gray-500 gap-4">
            <p>&copy; {{ date('Y') }} GraduFest. Todos los derechos reservados.</p>
        </div>

    </div>
</footer>
</body>
</html>