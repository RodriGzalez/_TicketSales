@extends('layouts.app')

@section('title', 'Próximos Eventos - GraduFest')

@section('content')
    <!-- Hero Banner (Inspirado en Ticketmaster) -->
    <section class="bg-gradient-to-r from-indigo-950 to-indigo-800 text-white py-16 px-4 text-center">
        <h2 class="text-4xl font-extrabold mb-3">Encuentra tus boletos para la gran celebración</h2>
        <p class="text-lg text-indigo-200 mb-6">Asegura tu lugar en las ceremonias de graduación más esperadas del año.</p>
    </section>
    <!-- Hero Banner Destacado (Estilo Ticketmaster) -->
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="relative bg-gradient-to-r from-gray-900 via-indigo-950 to-gray-900 rounded-2xl overflow-hidden shadow-2xl border border-indigo-900/50">
        
        <!-- Imagen de fondo con opacidad -->
        <div class="absolute inset-0 z-0 opacity-40 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200&auto=format&fit=crop');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/60 to-transparent z-0"></div>

        <!-- Contenido del Banner -->
        <div class="relative z-10 p-8 md:p-12 flex flex-col justify-end min-h-[380px]">
            
            <!-- Etiquetas superiores -->
            <div class="flex items-center gap-2 mb-3">
                <span class="bg-amber-600/90 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Universidad</span>
                <span class="bg-indigo-600/80 text-indigo-100 text-xs font-medium px-3 py-1 rounded-full">Últimos Lugares</span>
            </div>

            <!-- Título del evento destacado -->
            <h2 class="text-3xl md:text-5xl font-black text-white mb-3 leading-tight">
                Gran Gala de Graduación ITESM 2026
            </h2>

            <!-- Descripción corta -->
            <p class="text-gray-300 text-sm md:text-base max-w-2xl mb-6 line-clamp-2">
                La velada magna de graduación para los graduandos de las facultades de Ingeniería y Negocios. Incluye alfombra roja, banquete Gourmet de 3 tiempos, grupo de música en vivo y más.
            </p>

            <!-- Botones de acción y fecha -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center gap-2">
                        Comprar Boletos Ahora 
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <span class="text-indigo-200 text-sm font-medium flex items-center gap-1.5">
                        📅 15 de Julio, 2026 - 20:00 hrs
                    </span>
                </div>

                <!-- Controles del carrusel simulados -->
                <div class="flex items-center gap-2 self-end sm:self-center bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                    <button class="text-gray-400 hover:text-white p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="flex gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span class="w-2 h-2 rounded-full bg-white/40"></span>
                        <span class="w-2 h-2 rounded-full bg-white/40"></span>
                        <span class="w-2 h-2 rounded-full bg-white/40"></span>
                    </div>
                    <button class="text-gray-400 hover:text-white p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
    <!-- PLP: Catálogo de Eventos -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h3 class="text-2xl font-bold mb-8 text-gray-900">Próximos Eventos Disponibles</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                <!-- Tarjeta de Evento -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col justify-between">
                    <div>
                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">
                                {{ \Carbon\Carbon::parse($event->date)->format('d M, Y - h:i A') }}
                            </span>
                            <h4 class="text-xl font-bold text-gray-900 mt-1 mb-2">{{ $event->title }}</h4>
                            <p class="text-gray-600 text-sm mb-4">📍 {{ $event->location }}</p>
                            <p class="text-gray-500 text-sm line-clamp-2">{{ $event->description }}</p>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <a href="{{ route('events.show', $event->id) }}" class="block w-full text-center bg-indigo-600 text-white font-semibold py-2.5 rounded-lg hover:bg-indigo-700 transition">
                            Ver Boletos y Seleccionar
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 col-span-3 text-center py-10">No hay eventos disponibles en este momento.</p>
            @endforelse
        </div>
    </div>
@endsection