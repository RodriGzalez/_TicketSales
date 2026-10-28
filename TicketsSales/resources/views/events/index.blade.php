<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GraduFest - Próximos Eventos</title>
    <!-- Incluyendo Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Header / Navbar sencillo -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-black text-indigo-600">🎓 GraduFest</h1>
            <div>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-indigo-600 mr-4">Mis Boletos</a>
                <a href="#" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700">Iniciar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Hero Banner (Inspirado en Ticketmaster) -->
    <section class="bg-gradient-to-r from-indigo-900 to-purple-800 text-white py-16 px-4 text-center">
        <h2 class="text-4xl font-extrabold mb-3">Encuentra tus boletos para la gran celebración</h2>
        <p class="text-lg text-indigo-200 mb-6">Asegura tu lugar en las ceremonias de graduación más esperadas del año.</p>
    </section>

    <!-- PLP: Catálogo de Eventos -->
    <main class="max-w-7xl mx-auto px-4 py-12">
        <h3 class="text-2xl font-bold mb-8">Próximos Eventos Disponibles</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                <!-- Tarjeta de Evento -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col justify-between">
                    <div>
                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">{{ \Carbon\Carbon::parse($event->date)->format('d M, Y - h:i A') }}</span>
                            <h4 class="text-xl font-bold text-gray-900 mt-1 mb-2">{{ $event->title }}</h4>
                            <p class="text-gray-600 text-sm mb-4">📍 {{ $event->location }}</p>
                            <p class="text-gray-500 text-sm line-clamp-2">{{ $event->description }}</p>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <a href="#" class="block w-full text-center bg-indigo-600 text-white font-semibold py-2.5 rounded-lg hover:bg-indigo-700 transition">
                            Ver Boletos y Seleccionar
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 col-span-3 text-center py-10">No hay eventos disponibles en este momento.</p>
            @endforelse
        </div>
    </main>

</body>
</html>